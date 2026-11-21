<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventRegisteredNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function event(array $attrs = [], array $capacities = [15, 15]): Event
    {
        $event = Event::create($attrs + [
            'title' => 'Kelas Formulasi', 'format' => 'offline', 'event_date' => today()->addWeek(), 'timezone' => 'WITA',
            'venue' => 'EGO Corner Cafe', 'is_published' => true, 'registration_open' => true,
        ]);
        foreach ($capacities as $i => $capacity) {
            $event->sessions()->create(['label' => 'Sesi '.($i + 1), 'starts_at' => ['14:00', '15:30', '17:00'][$i], 'capacity' => $capacity, 'order' => $i]);
        }

        return $event;
    }

    private function learner(string $name = 'Ani'): User
    {
        return User::factory()->create(['name' => $name, 'username' => strtolower($name), 'origin_type' => 'umum'])->assignRole('student');
    }

    public function test_guests_see_the_event_but_must_log_in_to_sign_up(): void
    {
        $event = $this->event();

        $this->get(route('events.index'))->assertOk()->assertSee('Kelas Formulasi');
        $this->get(route('events.show', $event))->assertOk()->assertSee('EGO Corner Cafe')->assertSee('Daftar sekarang')->assertSee('Sisa 15');

        // "Masuk" and "Buat akun" both come back to the form after signing in.
        $this->get(route('events.join', $event))->assertRedirect(route('login'));
        $this->assertSame(route('events.join', $event), session('url.intended'));
        $this->get(route('events.join', [$event, 'baru' => 1]))->assertRedirect(route('register'));

        $this->post(route('events.register', $event), [])->assertRedirect(route('login'));
    }

    public function test_learner_signs_up_with_account_details_and_gets_an_email(): void
    {
        Notification::fake();
        $event = $this->event();
        $session = $event->sessions->first();
        $ani = $this->learner();

        // The form has its own page with the account's name and email already filled in.
        $this->actingAs($ani)->get(route('events.join', $event))->assertOk()->assertSee($ani->email)->assertSee('Sisa 15 kursi');

        $this->post(route('events.register', $event), ['event_session_id' => $session->id, 'whatsapp' => '0812-3456-7890', 'age' => 22])
            ->assertRedirect(route('events.success', $event));

        $this->assertDatabaseHas('event_registrations', ['user_id' => $ani->id, 'name' => 'Ani', 'email' => $ani->email, 'whatsapp' => '081234567890', 'age' => 22]);
        $this->assertSame('081234567890', $ani->fresh()->phone);
        Notification::assertSentTo($ani, EventRegisteredNotification::class);

        $this->get(route('events.success', $event))->assertSee('Hore, kamu terdaftar!')->assertSee($event->registrations()->value('code'));
        $this->get(route('events.show', $event))->assertSee('Kamu sudah terdaftar')->assertSee('Lihat tiket saya');
        // Once signed up, the form page sends the learner back to their ticket.
        $this->get(route('events.join', $event))->assertRedirect(route('events.show', $event).'#tiket');
        $this->get(route('dashboard'))->assertSee('Kelas Formulasi')->assertSee('Sesi 1');

        // One registration per person per event.
        $this->post(route('events.register', $event), ['event_session_id' => $event->sessions->last()->id, 'whatsapp' => '081234567890', 'age' => 22])
            ->assertSessionHasErrors('event_session_id');
        $this->assertSame(1, $event->registrations()->count());

        // Cancelling frees the seat.
        $this->delete(route('events.cancel', $event));
        $this->assertSame(0, $event->registrations()->count());
    }

    public function test_a_full_session_takes_no_more_sign_ups(): void
    {
        Notification::fake();
        $event = $this->event([], [1]);
        $session = $event->sessions->first();
        $form = ['event_session_id' => $session->id, 'whatsapp' => '081234567890', 'age' => 30];

        $this->actingAs($this->learner('Ani'))->post(route('events.register', $event), $form)->assertSessionHasNoErrors();
        $this->actingAs($this->learner('Budi'))->post(route('events.register', $event), $form)->assertSessionHasErrors('event_session_id');
        $this->get(route('events.show', $event))->assertSee('Penuh')->assertSee('Semua sesi sudah penuh');
        $this->assertSame(1, $session->registrations()->count());

        // Bad input is rejected before a seat is taken.
        $this->post(route('events.register', $event), ['event_session_id' => $session->id, 'whatsapp' => '12345', 'age' => 3])
            ->assertSessionHasErrors(['whatsapp', 'age']);
    }

    public function test_online_link_is_only_shown_to_registered_learners(): void
    {
        Notification::fake();
        $event = $this->event(['format' => 'online', 'venue' => null, 'meeting_url' => 'https://zoom.us/j/123456789'], [100]);
        $ani = $this->learner();

        $this->get(route('events.show', $event))->assertDontSee('zoom.us/j/123456789');
        $this->actingAs($ani)->get(route('events.show', $event))->assertDontSee('zoom.us/j/123456789');

        $this->post(route('events.register', $event), ['event_session_id' => $event->sessions->first()->id, 'whatsapp' => '081234567890', 'age' => 25]);
        $this->get(route('events.show', $event))->assertSee('zoom.us/j/123456789');
        $this->get(route('dashboard'))->assertSee('zoom.us/j/123456789');
    }

    public function test_drafts_and_past_events_take_no_sign_ups(): void
    {
        $draft = $this->event(['title' => 'Draf', 'is_published' => false]);
        $past = $this->event(['title' => 'Lewat', 'event_date' => today()->subDay()]);
        $ani = $this->learner();

        $this->get(route('events.show', $draft))->assertNotFound();
        $this->get(route('events.index'))->assertDontSee('Draf');
        $this->actingAs($ani)->post(route('events.register', $past), ['event_session_id' => $past->sessions->first()->id, 'whatsapp' => '081234567890', 'age' => 20])
            ->assertForbidden();
        $this->get(route('events.show', $past))->assertSee('sudah berlangsung');
    }

    public function test_admin_creates_and_edits_an_event_with_sessions(): void
    {
        Storage::fake('public');
        Notification::fake();
        $admin = User::factory()->create(['username' => 'adm'])->assignRole('admin');
        $this->actingAs($this->learner())->get(route('admin.events.index'))->assertForbidden();

        $form = [
            'title' => 'Kelas Formulasi', 'subtitle' => 'Solid Perfume & Make Up Remover', 'format' => 'offline',
            'event_date' => today()->addDays(4)->format('Y-m-d'), 'timezone' => 'WITA', 'venue' => 'EGO Corner Cafe',
            'is_published' => 1, 'registration_open' => 1,
            'cover' => UploadedFile::fake()->image('cover.jpg', 800, 1000),
            'sessions' => [['label' => 'Sesi 1', 'starts_at' => '14:00', 'capacity' => 15], ['label' => 'Sesi 2', 'starts_at' => '15:30', 'capacity' => 15]],
        ];
        $this->actingAs($admin)->post(route('admin.events.store'), $form)->assertRedirect(route('admin.events.index'));

        $event = Event::firstOrFail();
        $this->assertSame('kelas-formulasi', $event->slug);
        $this->assertSame(['Sesi 1', 'Sesi 2'], $event->sessions->pluck('label')->all());
        Storage::disk('public')->assertExists($event->cover);

        // Someone signs up for session 2; that session can't be removed or shrunk below its sign-ups.
        [$s1, $s2] = $event->sessions;
        $event->registrations()->create(['event_session_id' => $s2->id, 'user_id' => $this->learner('Budi')->id, 'name' => 'Budi', 'email' => 'b@x.id', 'whatsapp' => '081234567890', 'age' => 30]);
        $edit = ['cover' => null] + $form;
        unset($edit['cover']);

        $this->put(route('admin.events.update', $event), ['sessions' => [['id' => $s1->id, 'label' => 'Sesi 1', 'starts_at' => '14:00', 'capacity' => 15]]] + $edit)
            ->assertSessionHasErrors('sessions');
        $this->put(route('admin.events.update', $event), ['sessions' => [['id' => $s2->id, 'label' => 'Sesi 2', 'starts_at' => '15:30', 'capacity' => 0]]] + $edit)
            ->assertSessionHasErrors('sessions.0.capacity');

        // Removing the empty session and adding a new one works; the old cover is kept when none is uploaded.
        $this->put(route('admin.events.update', $event), ['title' => 'Kelas Formulasi Bali', 'sessions' => [
            ['id' => $s2->id, 'label' => 'Sesi 2', 'starts_at' => '15:30', 'capacity' => 20],
            ['id' => null, 'label' => 'Sesi 3', 'starts_at' => '17:00', 'ends_at' => '18:00', 'capacity' => 10],
        ]] + $edit)->assertRedirect(route('admin.events.index'));

        $event->refresh();
        $this->assertSame('kelas-formulasi', $event->slug);
        $this->assertSame(['Sesi 2' => 20, 'Sesi 3' => 10], $event->sessions->pluck('capacity', 'label')->all());
        Storage::disk('public')->assertExists($event->cover);

        $this->get(route('admin.events.registrations', $event))->assertOk()->assertSee('Budi')->assertSee('wa.me/6281234567890', false);
        $csv = $this->get(route('admin.events.registrations.export', $event))->streamedContent();
        $this->assertStringContainsString('Budi;b@x.id;081234567890;30', $csv);
    }

    public function test_description_formatting_is_escaped(): void
    {
        $event = new Event(['description' => "Halo <b>semua</b>
baris dua

## Cocok untuk
- Pemula
- Mahasiswa"]);

        $this->assertSame(
            '<p>Halo &lt;b&gt;semua&lt;/b&gt;<br>baris dua</p><h3>Cocok untuk</h3><ul><li>Pemula</li><li>Mahasiswa</li></ul>',
            $event->descriptionHtml()
        );
        $this->assertSame('Halo <b>semua</b> baris dua Cocok untuk Pemula Mahasiswa', $event->plainDescription());
    }

    public function test_ticket_has_a_qr_code_that_staff_scan_to_check_people_in(): void
    {
        $event = $this->event();
        $ani = $this->learner();
        $reg = $event->registrations()->create(['event_session_id' => $event->sessions->first()->id, 'user_id' => $ani->id, 'name' => 'Ani Lestari', 'email' => 'ani@x.id', 'whatsapp' => '081234567890', 'age' => 30]);
        $this->assertMatchesRegularExpression('/^RA-[2-9A-HJKMNP-Z]{6}$/', $reg->code);
        $url = route('tickets.show', $reg);

        // The owner sees the full ticket with its QR and can download it as a PDF.
        $this->actingAs($ani)->get($url)->assertOk()->assertSee('Tiket kamu')->assertSee('<svg', false)->assertSee('Ani Lestari')->assertSee('calendar.google.com', false);
        $pdf = $this->get(route('tickets.download', $reg))->assertOk();
        $this->assertSame('application/pdf', $pdf->headers->get('content-type'));

        // Anyone else who scans it only learns that the ticket is real.
        $budi = $this->learner('Budi');
        $this->actingAs($budi)->get($url)->assertOk()->assertSee('Tiket sah')->assertSee('A** L*****')->assertDontSee('081234567890')->assertDontSee('ani@x.id');
        $this->get(route('tickets.download', $reg))->assertForbidden();
        $this->post(route('tickets.checkin', $reg))->assertForbidden();
        auth()->logout();
        $this->get($url)->assertOk()->assertSee('Tiket sah');

        // Staff at the door: scan, check in, or type the code when the camera can't read it.
        $admin = User::factory()->create(['username' => 'adm'])->assignRole('admin');
        $this->actingAs($admin)->get($url)->assertSee('Mode panitia')->assertSee('Tandai hadir');
        $this->post(route('tickets.checkin', $reg))->assertRedirect();
        $this->assertNotNull($reg->fresh()->checked_in_at);
        $this->get(route('admin.events.registrations', $event))->assertSee($reg->code)->assertSee('1 sudah hadir');
        $this->get(route('tickets.lookup', ['kode' => strtolower(substr($reg->code, 3))]))->assertRedirect($url);
        $this->from(route('admin.events.registrations', $event))->get(route('tickets.lookup', ['kode' => 'RA-XXXXXX']))->assertSessionHas('lookup_error');

        // The confirmation email carries the PDF ticket.
        $mail = (new EventRegisteredNotification($reg->load('event', 'session')))->toMail($ani);
        $this->assertCount(1, $mail->rawAttachments);
        $this->assertStringStartsWith('%PDF', $mail->rawAttachments[0]['data']);
    }

    public function test_admin_copies_an_event_for_the_next_edition(): void
    {
        $event = $this->event();
        $admin = User::factory()->create(['username' => 'adm'])->assignRole('admin');

        $this->actingAs($admin)->post(route('admin.events.duplicate', $event));
        $copy = Event::latest('id')->first();

        $this->assertNotSame($event->id, $copy->id);
        $this->assertSame('Kelas Formulasi (salinan)', $copy->title);
        $this->assertFalse($copy->is_published);
        $this->assertSame(['Sesi 1', 'Sesi 2'], $copy->sessions->pluck('label')->all());
    }
}
