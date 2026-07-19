<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Notification $notification;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Notification $notification
    ) {
        $this->notification = $notification;
    }

    /**
     * Build the email.
     */
    public function build()
    {
        return $this->subject(
                $this->notification->subject
                    ?? 'Smart Care Notification'
            )
            ->view('emails.notifications.general')
            ->with([
                'notification' => $this->notification,
            ]);
    }
}