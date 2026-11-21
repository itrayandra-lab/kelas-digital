<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('registrations')->withSum('sessions as capacity_total', 'capacity')
            ->orderByDesc('event_date')->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $event = new Event(['format' => 'offline', 'timezone' => 'WIB', 'registration_open' => true]);
        $sessions = [['id' => null, 'label' => 'Sesi 1', 'starts_at' => '', 'ends_at' => '', 'capacity' => 15]];

        return view('admin.events.form', compact('event', 'sessions'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        $event = DB::transaction(function () use ($request, $data) {
            $event = Event::create($data);
            $this->syncSessions($event, $request->input('sessions'));

            return $event;
        });

        return redirect()->route('admin.events.index')->with('success', "Event \"{$event->title}\" dibuat.".($event->is_published ? '' : ' Event belum tampil di website sampai kamu centang "Tampilkan di website".'));
    }

    public function edit(Event $event)
    {
        $sessions = $event->sessions()->withCount('registrations')->get()
            ->map(fn ($s) => ['id' => $s->id, 'label' => $s->label, 'starts_at' => substr($s->starts_at, 0, 5), 'ends_at' => $s->ends_at ? substr($s->ends_at, 0, 5) : '', 'capacity' => $s->capacity, 'registered' => $s->registrations_count])
            ->all();

        return view('admin.events.form', compact('event', 'sessions'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validated($request, $event);
        $oldCover = $event->cover;

        DB::transaction(function () use ($request, $event, $data) {
            $event->update($data);
            $this->syncSessions($event, $request->input('sessions'));
        });

        if ($oldCover && $oldCover !== $event->cover) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('admin.events.index')->with('success', "Event \"{$event->title}\" disimpan.");
    }

    public function destroy(Event $event)
    {
        if ($event->cover) {
            Storage::disk('public')->delete($event->cover);
        }
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event dihapus beserta daftar pesertanya.');
    }

    public function registrations(Request $request, Event $event)
    {
        $event->load(['sessions' => fn ($q) => $q->withCount('registrations')]);
        $session = $event->sessions->firstWhere('id', (int) $request->query('sesi'));
        $search = trim((string) $request->query('q'));

        $registrations = $event->registrations()->with('session', 'user')
            ->when($session, fn ($q) => $q->where('event_session_id', $session->id))
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('whatsapp', 'like', "%{$search}%")))
            ->oldest()
            ->get();

        return view('admin.events.registrations', compact('event', 'registrations', 'session', 'search'));
    }

    public function exportRegistrations(Event $event): StreamedResponse
    {
        $rows = $event->registrations()->with('session', 'user')->oldest()->get();

        return response()->streamDownload(function () use ($rows, $event) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads the names as UTF-8
            // Semicolons: Excel with Indonesian regional settings splits columns on ";".
            fputcsv($out, ['Nama', 'Email', 'WhatsApp', 'Usia', 'Asal', 'Universitas / kantor', 'Sesi', 'Jam', 'Waktu daftar'], ';');
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->name, $r->email, $r->whatsapp, $r->age,
                    User::ORIGINS[$r->user?->origin_type] ?? '', $r->user?->institution,
                    $r->session->label, $r->session->timeLabel().' '.$event->timezone, $r->created_at->format('Y-m-d H:i'),
                ], ';');
            }
            fclose($out);
        }, 'peserta-event-'.$event->slug.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function destroyRegistration(EventRegistration $registration)
    {
        $event = $registration->event;
        $registration->delete();

        return redirect()->route('admin.events.registrations', $event)->with('success', "Pendaftaran {$registration->name} dihapus, kursinya terbuka lagi.");
    }

    private function validated(Request $request, ?Event $event): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:60',
            'cover' => [$event?->cover ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'description' => 'nullable|string|max:10000',
            'format' => ['required', Rule::in(array_keys(Event::FORMATS))],
            'event_date' => 'required|date',
            'timezone' => ['required', Rule::in(Event::TIMEZONES)],
            'venue' => 'required_if:format,offline|nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'maps_url' => 'nullable|url|max:500',
            'meeting_url' => 'nullable|url|max:500',
            'is_published' => 'boolean',
            'registration_open' => 'boolean',
            'sessions' => 'required|array|min:1|max:20',
            'sessions.*.id' => 'nullable|integer',
            'sessions.*.label' => 'required|string|max:60',
            'sessions.*.starts_at' => 'required|date_format:H:i',
            'sessions.*.ends_at' => 'nullable|date_format:H:i|after:sessions.*.starts_at',
            'sessions.*.capacity' => 'required|integer|min:1|max:100000',
        ], [
            'cover.required' => 'Upload gambar cover event.',
            'venue.required_if' => 'Isi nama tempat untuk event offline.',
            'sessions.required' => 'Tambahkan minimal satu sesi.',
            'sessions.*.label.required' => 'Setiap sesi perlu nama, misalnya "Sesi 1".',
            'sessions.*.starts_at.required' => 'Isi jam mulai setiap sesi.',
            'sessions.*.ends_at.after' => 'Jam selesai harus setelah jam mulai.',
            'sessions.*.capacity.required' => 'Isi kuota peserta setiap sesi.',
        ]);

        unset($data['sessions']);
        $data['is_published'] = $request->boolean('is_published');
        $data['registration_open'] = $request->boolean('registration_open');
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('events', 'public');
        } else {
            unset($data['cover']);
        }

        return $data;
    }

    /** Updates the sessions kept in the form, adds new ones, and removes the rest unless someone already signed up for them. */
    private function syncSessions(Event $event, array $rows): void
    {
        $existing = $event->sessions()->withCount('registrations')->get()->keyBy('id');
        $kept = [];

        foreach (array_values($rows) as $i => $row) {
            $attrs = ['label' => $row['label'], 'starts_at' => $row['starts_at'], 'ends_at' => ($row['ends_at'] ?? null) ?: null, 'capacity' => (int) $row['capacity'], 'order' => $i];
            $session = $existing->get((int) ($row['id'] ?? 0));

            if ($session && $attrs['capacity'] < $session->registrations_count) {
                throw ValidationException::withMessages(["sessions.{$i}.capacity" => "Kuota {$session->label} tidak bisa di bawah {$session->registrations_count}, karena sudah ada {$session->registrations_count} pendaftar."]);
            }
            $session ? $session->update($attrs) : $session = $event->sessions()->create($attrs);
            $kept[] = $session->id;
        }

        foreach ($existing->except($kept) as $removed) {
            if ($removed->registrations_count > 0) {
                throw ValidationException::withMessages(['sessions' => "{$removed->label} tidak bisa dihapus karena sudah ada {$removed->registrations_count} pendaftar. Hapus pendaftarnya dulu di halaman Peserta."]);
            }
            $removed->delete();
        }
    }
}
