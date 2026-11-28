<?php

namespace App\Notifications;

use App\Http\Controllers\TicketController;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Queued: building the ticket PDF and talking to the mail server must not keep the sign-up page waiting.
class EventRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EventRegistration $registration)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->registration->event;
        $session = $this->registration->session;

        $mail = (new MailMessage)
            ->subject('Pendaftaran berhasil: '.$event->title)
            ->greeting('Halo '.$this->registration->name.'!')
            ->line('Kamu sudah terdaftar di **'.$event->title.'**.')
            ->line('**Tanggal:** '.$event->dateLabel())
            ->line('**Sesi:** '.$session->label.', pukul '.$session->timeLabel().' '.$event->timezone)
            ->line('**Kode tiket:** '.$this->registration->code);

        if ($event->isOnline()) {
            $mail->line($event->meeting_url
                ? '**Link acara:** '.$event->meeting_url
                : 'Link acara akan muncul di halaman event dan dashboard-mu menjelang acara.');
        } else {
            $mail->line('**Lokasi:** '.$event->venue.($event->address ? ', '.$event->address : ''));
        }

        return $mail
            ->line('Tiketmu terlampir sebagai PDF. Tunjukkan QR di tiket ke panitia saat datang, dari HP atau hasil cetak.')
            ->action('Lihat tiket saya', route('tickets.show', $this->registration))
            ->line('Kalau berhalangan hadir, batalkan pendaftaranmu dari halaman tiket supaya kursinya bisa dipakai peserta lain.')
            ->attachData(TicketController::pdf($this->registration)->output(), 'tiket-'.$this->registration->code.'.pdf', ['mime' => 'application/pdf'])
            ->salutation('Sampai jumpa, '.config('app.name'));
    }
}
