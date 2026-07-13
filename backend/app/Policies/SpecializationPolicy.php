<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Specialization;

class SpecializationPolicy
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
        Specialization $specialization
    ): bool
    {
        return
            $user->hasRole('platform_admin') ||
            $user->hasRole('hospital_admin') ||
            $user->hasRole('doctor') ||
            $user->hasRole('patient');
    }


    public function create(User $user): bool
    {
        return $user->hasRole('platform_admin');
    }


    public function update(
        User $user,
        Specialization $specialization
    ): bool
    {
        return $user->hasRole('platform_admin');
    }


    public function delete(
        User $user,
        Specialization $specialization
    ): bool
    {
        return $user->hasRole('platform_admin');
    }

}