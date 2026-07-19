<?php

namespace App\Policies;

use App\Models\MedicalEncounter;
use App\Models\User;

class MedicalEncounterPolicy
{

    public function view(User $user, MedicalEncounter $encounter): bool
    {
        if ($user->hasRole('doctor')) {
            $doctorId = $user->healthcareProvider?->id ?? $user->id;
            return $encounter->doctor_id === $doctorId;
        }

        if ($user->hasRole('patient')) {
            $patientId = $user->patient?->id ?? $user->id;
            return $encounter->patient_id === $patientId;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('doctor');
    }

    public function update(User $user, MedicalEncounter $encounter): bool
    {
        if (!$user->hasRole('doctor')) {
            return false;
        }
        $doctorId = $user->healthcareProvider?->id ?? $user->id;
        return $encounter->doctor_id === $doctorId;
    }

    public function delete(User $user, MedicalEncounter $encounter): bool
    {
        return false;
    }

    public function complete(User $user, MedicalEncounter $encounter): bool
    {
        if (!$user->hasRole('doctor')) {
            return false;
        }
        $doctorId = $user->healthcareProvider?->id ?? $user->id;
        return $encounter->doctor_id === $doctorId;
    }
}