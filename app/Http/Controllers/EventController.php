<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventSession;
use App\Notifications\EventRegisteredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    public function index()
    {
        $seats = ['sessions' => fn ($q) => $q->withCount('registrations')];
        $upcoming = Event::published()->upcoming()->with($seats)->get();
        $past = Event::published()->whereDate('event_date', '<', today())->latest('event_date')->limit(6)->get();

        return view('events.index', compact('upcoming', 'past'));
    }

    public function show(Request $request, Event $event)
    {
        // Admins can preview a draft before publishing it.
        abort_unless($event->is_published || $request->user()?->can('manage events'), 404);

        $event->load(['sessions' => fn ($q) => $q->withCount('registrations')]);
        $registration = $request->user()
            ? $event->registrations()->with('session')->where('user_id', $request->user()->id)->first()
            : null;

        return view('events.show', compact('event', 'registration'));
    }

    /**
     * The sign-up form, on its own page. Guests are sent to log in (or create an account with ?baru=1)
     * and land back here afterwards; people already signed up see their ticket on the event page instead.
     */
    public function join(Request $request, Event $event)
    {
        abort_unless($event->is_published || $request->user()?->can('manage events'), 404);
        $user = $request->user();

        if (! $user) {
            redirect()->setIntendedUrl(route('events.join', $event));

            return redirect()->route($request->boolean('baru') ? 'register' : 'login');
        }
        if (! $event->acceptsRegistrations() || $event->registrations()->where('user_id', $user->id)->exists()) {
            return redirect()->to(route('events.show', $event).'#tiket');
        }

        $event->load(['sessions' => fn ($q) => $q->withCount('registrations')]);

        return view('events.register', compact('event', 'user'));
    }

    public function register(Request $request, Event $event)
    {
        abort_unless($event->acceptsRegistrations(), 403, 'Pendaftaran event ini sudah ditutup.');
        $user = $request->user();

        $request->merge(['whatsapp' => preg_replace('/[^0-9+]/', '', (string) $request->input('whatsapp'))]);
        $data = $request->validate([
            'event_session_id' => ['required', 'integer'],
            'whatsapp' => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'age' => ['required', 'integer', 'min:10', 'max:100'],
        ], [
            'event_session_id.required' => 'Pilih sesi yang ingin kamu ikuti.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid. Contoh: 081234567890.',
            'age.required' => 'Usia wajib diisi.',
            'age.min' => 'Usia minimal 10 tahun.',
            'age.max' => 'Usia tidak valid.',
        ]);

        $registration = DB::transaction(function () use ($event, $user, $data) {
            // Locking the session row makes simultaneous sign-ups for the last seat wait in line.
            $session = EventSession::where('event_id', $event->id)->lockForUpdate()->find($data['event_session_id']);

            if (! $session) {
                throw ValidationException::withMessages(['event_session_id' => 'Pilih sesi yang ingin kamu ikuti.']);
            }
            if ($event->registrations()->where('user_id', $user->id)->exists()) {
                throw ValidationException::withMessages(['event_session_id' => 'Kamu sudah terdaftar di event ini.']);
            }
            if ($session->registrations()->count() >= $session->capacity) {
                throw ValidationException::withMessages(['event_session_id' => "Maaf, {$session->label} sudah penuh. Silakan pilih sesi lain."]);
            }

            return $event->registrations()->create([
                'event_session_id' => $session->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'whatsapp' => $data['whatsapp'],
                'age' => $data['age'],
            ]);
        });

        // Remembered so the next event form is already filled in.
        $user->update(['phone' => $data['whatsapp']]);

        try {
            $user->notify(new EventRegisteredNotification($registration->load('event', 'session')));
        } catch (\Throwable $e) {
            logger('Gagal kirim email pendaftaran event ke '.$user->email.': '.$e->getMessage());
        }

        return redirect()->route('events.success', $event);
    }

    /** The "you're in" page right after signing up: a celebration, the ticket, and plain next steps. */
    public function success(Request $request, Event $event)
    {
        $registration = $event->registrations()->with('session')->where('user_id', $request->user()->id)->first();
        if (! $registration) {
            return redirect()->route('events.show', $event);
        }

        return view('events.success', compact('event', 'registration'));
    }

    public function cancel(Request $request, Event $event)
    {
        abort_if($event->isPast(), 403, 'Event sudah selesai.');
        EventRegistration::where('event_id', $event->id)->where('user_id', $request->user()->id)->delete();

        return redirect()->to(route('events.show', $event).'#tiket')->with('event_cancelled', true);
    }
}
