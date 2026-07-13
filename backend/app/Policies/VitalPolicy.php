<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vital;

class VitalPolicy
{
    public function view(User $user, Vital $vital): bool
    {
        if ($user->hasRole('platform_admin')) return true;

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $vital->encounter?->hospital_id)
                ->exists();
        }

        if ($user->hasRole('doctor')) {
            return $vital->encounter?->doctor_id === $user->id;
        }

        if ($user->hasRole('patient')) {
            return $vital->patient_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('doctor');
    }

    public function update(User $user, Vital $vital): bool
    {
        if ($user->hasRole('platform_admin')) return true;

        if ($user->hasRole('doctor')) {
            return $vital->encounter?->doctor_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Vital $vital): bool
    {
        return $user->hasRole('platform_admin');
    }
}
