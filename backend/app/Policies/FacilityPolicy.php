<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('platform_admin')
            || $user->hasRole('hospital_admin');
    }

    public function view(
        User $user,
        Facility $facility
    ): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where(
                'hospital_id',
                $facility->hospital_id
            )
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasRole('platform_admin')
            || $user->hasRole('hospital_admin');
    }

    public function update(
        User $user,
        Facility $facility
    ): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where(
                'hospital_id',
                $facility->hospital_id
            )
            ->exists();
    }

    public function delete(
        User $user,
        Facility $facility
    ): bool
    {
        return $this->update(
            $user,
            $facility
        );
    }
}