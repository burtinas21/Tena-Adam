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
    public string $roleName;
    public string $roleLabel;

    public function __construct(
        User $user,
        string $plainToken,
        ?string $roleName = null
    ) {
        $this->user = $user;

        // Get role from the argument first.
        // If not provided, get it from the user's assigned role.
        $this->roleName = $roleName
            ?? $user->roles()->value('name')
            ?? 'staff';

        $this->roleLabel = match ($this->roleName) {
            'hospital_admin' => 'Hospital Administrator',
            'doctor'         => 'Doctor',
            'receptionist'   => 'Receptionist',
            default          => ucwords(str_replace('_', ' ', $this->roleName)),
        };

        $this->activationUrl = config('app.frontend_url')
            . '/accept-invitation?token='
            . urlencode($plainToken)
            . '&email='
            . urlencode($user->email);
    }

    public function build(): self
    {
        return $this
            ->subject(
                'You have been invited to Smart Care – Set Your Password'
            )
            ->view('emails.staff-invitation')
            ->with([
                'user'          => $this->user,
                'activationUrl' => $this->activationUrl,
                'roleName'      => $this->roleName,
                'roleLabel'     => $this->roleLabel,
            ]);
    }
}