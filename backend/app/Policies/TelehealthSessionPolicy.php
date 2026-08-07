<?php

namespace App\Policies;

use App\Models\TelehealthSession;
use App\Models\User;

class TelehealthSessionPolicy
{
    public function view(User $user, TelehealthSession $session): bool
    {
        if ($user->hasAnyRole(['platform_admin', 'hospital_admin'])) {
            return true;
        }

        if (
            $user->hasRole('doctor') &&
            $user->hasPermission('create_telehealth_session') &&
            $user->healthcareProvider &&
            $user->healthcareProvider->id === $session->appointment->doctor_id
        ) {
            return true;
        }

        if (
            $user->hasRole('patient') &&
            $user->hasPermission('join_telehealth_session') &&
            $user->patient &&
            $user->patient->id === $session->appointment->patient_id
        ) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_telehealth_session');
    }

    public function update(User $user, TelehealthSession $session): bool
    {
        if (
            $user->hasRole('doctor') &&
            $user->hasPermission('create_telehealth_session') &&
            $user->healthcareProvider &&
            $user->healthcareProvider->id === $session->appointment->doctor_id
        ) {
            return true;
        }

        return $user->hasAnyRole(['hospital_admin', 'platform_admin']);
    }

    public function start(User $user, TelehealthSession $session): bool
    {
        return $this->update($user, $session);
    }

    public function complete(User $user, TelehealthSession $session): bool
    {
        return $this->update($user, $session);
    }

    public function cancel(User $user, TelehealthSession $session): bool
    {
        return $this->update($user, $session);
    }

    public function delete(User $user, TelehealthSession $session): bool
    {
        return $user->hasAnyRole(['hospital_admin', 'platform_admin']);
    }
}
