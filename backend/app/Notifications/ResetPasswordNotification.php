<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $url;

    public function __construct($url)
    {

        $this->url = $url;

    }

    public function via($notifiable)
    {

        return ['mail'];

    }

    public function toMail($notifiable)
    {

        return (new MailMessage)

            ->subject('TENA-ADAM Password Reset')

            ->line('Click the button below to reset your password.')

            ->action(
                'Reset Password',
                $this->url
            )

            ->line('If you did not request this, ignore this email.');

    }
}
