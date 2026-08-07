<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HealthcareProvider;

class HealthcareProviderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_doctors');
    }

    public function view(User $user, HealthcareProvider $provider): bool
    {
        if (!$user->hasPermission('view_doctors')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        // Patients can view any doctor profile (needed for browsing and booking)
        if ($user->hasRole('patient')) {
            return true;
        }

        return $user
            ->hospitals()
            ->where('hospital_id', $provider->hospital_id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_doctors');
    }

    public function update(User $user, HealthcareProvider $provider): bool
    {
        if ($user->hasRole('doctor') && $user->id === $provider->id) {
            return $user->hasPermission('update_doctors');
        }

        if ($user->hasRole('hospital_admin') && $user->hasPermission('update_doctors')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $provider->hospital_id)
                ->exists();
        }

        return false;
    }

    public function delete(User $user, HealthcareProvider $provider): bool
    {
        if (!$user->hasPermission('delete_doctors')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where('hospital_id', $provider->hospital_id)
            ->exists();
    }
}
