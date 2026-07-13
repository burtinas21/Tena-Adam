<?php

namespace App\Policies;

use App\Models\PatientEmergencyContact;
use App\Models\User;

class PatientEmergencyContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('patient');
    }

    public function view(User $user, PatientEmergencyContact $contact): bool
    {
        return $user->hasRole('patient')
            && $user->patient?->id === $contact->patient_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('patient');
    }

    public function update(User $user, PatientEmergencyContact $contact): bool
    {
        return $user->hasRole('patient')
            && $user->patient?->id === $contact->patient_id;
    }

    public function delete(User $user, PatientEmergencyContact $contact): bool
    {
        return $user->hasRole('patient')
            && $user->patient?->id === $contact->patient_id;
    }
}
