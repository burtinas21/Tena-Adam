<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DoctorSchedule;

class DoctorSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return
            $user->hasRole('platform_admin') ||
            $user->hasRole('hospital_admin') ||
            $user->hasRole('doctor')         ||
            $user->hasRole('receptionist');
    }

    public function view(
        User $user,
        DoctorSchedule $schedule
    ): bool {

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {

            return $user->hospitalStaff()
                ->where(
                    'hospital_id',
                    $schedule->doctor->hospital_id
                )
                ->exists();
        }

        if ($user->hasRole('doctor')) {

            return $schedule->doctor_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return
            $user->hasRole('hospital_admin') ||
            $user->hasRole('doctor');
    }

    public function update(User $user, DoctorSchedule $schedule): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

       if ($user->hasRole('hospital_admin')) {

    return $user->hospitalStaff()
        ->where(
            'hospital_id',
            $schedule->doctor->hospital_id
        )
        ->exists();
}

        // doctor can update ONLY own schedule
        return $user->hasRole('doctor')
            && $schedule->doctor_id === $user->id;
    }

    public function delete(User $user, DoctorSchedule $schedule): bool
{
    if ($user->hasRole('platform_admin')) {
        return true;
    }

  if ($user->hasRole('hospital_admin')) {

    return $user->hospitalStaff()
        ->where(
            'hospital_id',
            $schedule->doctor->hospital_id
        )
        ->exists();
}

    return $user->hasRole('doctor')
        && $schedule->doctor_id === $user->id;
}
}