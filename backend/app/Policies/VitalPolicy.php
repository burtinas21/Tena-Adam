<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vital;

class VitalPolicy
{
    /**
     * Create vital signs.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('doctor');
    }

    /**
     * View vital signs.
     */
    public function view(User $user, Vital $vital): bool
    {
        if (! $user->hasRole('doctor')) {
            return false;
        }

        return $user->healthcareProvider
            && $user->healthcareProvider->id === $vital->encounter->doctor_id;
    }

    /**
     * Update vital signs.
     */
    public function update(User $user, Vital $vital): bool
    {
        if (! $user->hasRole('doctor')) {
            return false;
        }

        return $user->healthcareProvider
            && $user->healthcareProvider->id === $vital->encounter->doctor_id;
    }

    /**
     * Delete vital signs.
     */
    public function delete(User $user, Vital $vital): bool
    {
        if (! $user->hasRole('doctor')) {
            return false;
        }

        return $user->healthcareProvider
            && $user->healthcareProvider->id === $vital->encounter->doctor_id;
    }
}