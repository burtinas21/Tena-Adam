<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PatientEmergencyContact;

class PatientEmergencyContactPolicy
{
    public function viewAny(User $user): bool
    {
        return
            $user->hasRole('patient') ||
            $user->hasRole('hospital_admin') ||
            $user->hasRole('platform_admin');
    }

    public function view(
        User $user,
        PatientEmergencyContact $contact
    ): bool {

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return true;
        }

        return
            $user->hasRole('patient')
            && $contact->patient_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('patient');
    }

    public function update(
        User $user,
        PatientEmergencyContact $contact
    ): bool {

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return
            $user->hasRole('patient')
            && $contact->patient_id === $user->id;
    }

    public function delete(
        User $user,
        PatientEmergencyContact $contact
    ): bool {

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return
            $user->hasRole('patient')
            && $contact->patient_id === $user->id;
    }
}