<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $activationUrl;

    public function __construct(User $user, string $plainToken)
    {
        $this->user = $user;

        $this->activationUrl = config('app.frontend_url')
            . '/accept-invitation?token='
            . urlencode($plainToken)
            . '&email='
            . urlencode($user->email);
    }

    public function build(): self
    {
        return $this
            ->subject('You have been invited to Smart Care – Set Your Password')
            ->view('emails.staff-invitation')
            ->with([
                'user'           => $this->user,
                'activationUrl'  => $this->activationUrl,
            ]);
    }
}
