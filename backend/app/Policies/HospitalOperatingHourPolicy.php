<?php

namespace App\Policies;

use App\Models\HospitalOperatingHour;
use App\Models\User;

class HospitalOperatingHourPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_operating_hours');
    }

    public function view(User $user, HospitalOperatingHour $hour): bool
    {
        if (!$user->hasPermission('view_operating_hours')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where('hospital_id', $hour->hospital_id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_operating_hours');
    }

    public function update(User $user, HospitalOperatingHour $hour): bool
    {
        if (!$user->hasPermission('update_operating_hours')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where('hospital_id', $hour->hospital_id)
            ->exists();
    }

    public function delete(User $user, HospitalOperatingHour $hour): bool
    {
        return $this->update($user, $hour);
    }
}
