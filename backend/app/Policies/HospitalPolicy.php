<?php

namespace App\Policies;

use App\Models\Hospital;
use App\Models\User;

class HospitalPolicy
{

    public function viewAny(User $user): bool
    {

        if ($user->hasRole('platform_admin')) {

            return true;

        }
         if ($user->hasRole('patient')) {

            return true;

        }

        return $user->hospitals()->exists();

    }



    public function view(
        User $user,
        Hospital $hospital
    ): bool {

        // Platform admins and patients can view any hospital
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('patient')) {
            return true;
        }

        // Hospital admins and doctors can only view their own hospital
        return $user->hospitals()
            ->where('hospital_id', $hospital->id)
            ->exists();

    }



    public function create(User $user): bool
    {

        return $user->hasRole(
            'platform_admin'
        );

    }



    public function update(
        User $user,
        Hospital $hospital
    ): bool
    {


        if ($user->hasRole('platform_admin')) {

            return true;

        }


        return false;

    }



    public function delete(
        User $user,
        Hospital $hospital
    ): bool
    {

        return $user->hasRole(
            'platform_admin'
        );

    }

}