<?php

namespace App\Policies;

use App\Models\MedicalEncounter;
use App\Models\User;

class MedicalEncounterPolicy
{

    public function view(User $user, MedicalEncounter $encounter): bool
    {
        return $user->hasRole('doctor')
            && $encounter->doctor_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('doctor');
    }

    
    public function update(User $user, MedicalEncounter $encounter): bool
    {
        return $user->hasRole('doctor')
            && $encounter->doctor_id === $user->id;
    }


    public function delete(User $user, MedicalEncounter $encounter): bool
    {
        return false;
    }
}