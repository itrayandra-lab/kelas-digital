<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(public User $user)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat Bergabung di '.config('app.name'))
            ->greeting('Halo '.$this->user->name.'!')
            ->line('Terima kasih telah mendaftar di **'.config('app.name').'**.')
            ->line('Akun Anda dengan username **'.$this->user->username.'** sudah aktif dan siap digunakan.')
            ->line('Anda kini dapat mengakses berbagai kursus dan materi pembelajaran yang tersedia.')
            ->action('Mulai Belajar', url('/dashboard'))
            ->line('Jika ada pertanyaan, jangan ragu untuk menghubungi tim dukungan kami.')
            ->salutation('Salam, '.config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
