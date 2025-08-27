<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Buat link ke route khusus customer.reset-password
        // Menambahkan email sebagai query param supaya field email terisi pada form reset
        $url = url(route('customer.reset-password', ['token' => $this->token], false)) . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi — Peace Picture Studio')
            ->greeting('Halo ' . ($notifiable->name ?? ''))
            ->line('Kamu menerima email ini karena ada permintaan untuk mengatur ulang kata sandi akunmu di Peace Picture Studio.')
            ->action('Atur Ulang Kata Sandi', $url)
            ->line('Jika kamu tidak meminta pengaturan ulang, kamu boleh mengabaikan email ini.')
            ->salutation('Salam, Peace Picture Studio');
    }
}
