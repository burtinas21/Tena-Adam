<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PatientEmergencyContact;

class PatientEmergencyContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_patients');
    }

    public function view(User $user, PatientEmergencyContact $contact): bool
    {
        if (!$user->hasPermission('view_patients')) {
            return false;
        }

        if ($user->hasRole('platform_admin') || $user->hasRole('hospital_admin')) {
            return true;
        }

        return $user->hasRole('patient') && $contact->patient_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('patient');
    }

    public function update(User $user, PatientEmergencyContact $contact): bool
    {
        if ($user->hasRole('platform_admin') && $user->hasPermission('update_patients')) {
            return true;
        }

        return $user->hasRole('patient') && $contact->patient_id === $user->id;
    }

    public function delete(User $user, PatientEmergencyContact $contact): bool
    {
        if ($user->hasRole('platform_admin') && $user->hasPermission('delete_patients')) {
            return true;
        }

        return $user->hasRole('patient') && $contact->patient_id === $user->id;
    }
}
