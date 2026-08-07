<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DoctorSchedule;

class DoctorSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_schedule')
            || $user->hasPermission('view_doctors');
    }

    public function view(User $user, DoctorSchedule $schedule): bool
    {
        if (!$user->hasPermission('manage_schedule') && !$user->hasPermission('view_doctors')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $schedule->doctor->hospital_id)
                ->exists();
        }

        if ($user->hasRole('doctor')) {
            return $schedule->doctor_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_schedule');
    }

    public function update(User $user, DoctorSchedule $schedule): bool
    {
        if (!$user->hasPermission('manage_schedule')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $schedule->doctor->hospital_id)
                ->exists();
        }

        // Doctor can update only their own schedule
        return $user->hasRole('doctor') && $schedule->doctor_id === $user->id;
    }

    public function delete(User $user, DoctorSchedule $schedule): bool
    {
        if (!$user->hasPermission('manage_schedule')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $schedule->doctor->hospital_id)
                ->exists();
        }

        return $user->hasRole('doctor') && $schedule->doctor_id === $user->id;
    }
}
