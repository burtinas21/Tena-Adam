<?php

namespace App\Policies;

use App\Models\MedicalEncounter;
use App\Models\User;

class MedicalEncounterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_emr');
    }

    public function view(User $user, MedicalEncounter $encounter): bool
    {
        if (!$user->hasPermission('view_emr')) {
            return false;
        }

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
        return $user->hasPermission('create_emr');
    }

    public function update(User $user, MedicalEncounter $encounter): bool
    {
        if (!$user->hasPermission('update_emr')) {
            return false;
        }

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
        if (!$user->hasPermission('update_emr')) {
            return false;
        }

        if (!$user->hasRole('doctor')) {
            return false;
        }

        $doctorId = $user->healthcareProvider?->id ?? $user->id;
        return $encounter->doctor_id === $doctorId;
    }
}
