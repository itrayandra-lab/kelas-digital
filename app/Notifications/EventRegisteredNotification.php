<?php

namespace App\Notifications;

use App\Models\EventRegistration;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventRegisteredNotification extends Notification
{
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
            ->line('**Sesi:** '.$session->label.', pukul '.$session->timeLabel().' '.$event->timezone);

        if ($event->isOnline()) {
            $mail->line($event->meeting_url
                ? '**Link acara:** '.$event->meeting_url
                : 'Link acara akan muncul di halaman event dan dashboard-mu menjelang acara.');
        } else {
            $mail->line('**Lokasi:** '.$event->venue.($event->address ? ', '.$event->address : ''));
        }

        return $mail
            ->action('Lihat detail event', route('events.show', $event))
            ->line('Kalau berhalangan hadir, batalkan pendaftaranmu di halaman event supaya kursinya bisa dipakai peserta lain.')
            ->salutation('Sampai jumpa, '.config('app.name'));
    }
}
