<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AppointmentSlot;

class AppointmentSlotPolicy
{
    public function viewAny(User $user): bool
    {
        return
            $user->hasRole('platform_admin') ||
            $user->hasRole('hospital_admin') ||
            $user->hasRole('doctor') ||
            $user->hasRole('patient');
    }

    public function view(
        User $user,
        AppointmentSlot $slot
    ): bool {

        return true;
    }

    public function create(User $user): bool
    {
        return
            $user->hasRole('hospital_admin') ||
            $user->hasRole('platform_admin');
    }


    public function update(
        User $user,
        AppointmentSlot $slot
    ): bool {

        return
            $user->hasRole('hospital_admin') ||
            $user->hasRole('platform_admin');
    }

 
    public function delete(
        User $user,
        AppointmentSlot $slot
    ): bool {

        return
            $user->hasRole('hospital_admin') ||
            $user->hasRole('platform_admin');
    }
}