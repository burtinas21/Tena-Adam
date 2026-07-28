<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{


    public function viewAny(User $user): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        // Doctors need to browse departments for the referral feature
        if ($user->hasRole('doctor') || $user->hasRole('receptionist') || $user->hasRole('patient')) {
            return true;
        }

        return $user->hospitals()->exists();
    }



    public function view(
        User $user,
        Department $department
    ): bool {


        if($user->hasRole('platform_admin')){

            return true;

        }



        return $user->hospitals()
            ->where(
                'hospitals.id',
                $department->hospital_id
            )
            ->exists();

    }




    public function create(User $user): bool
    {

        if($user->hasRole('platform_admin')){

            return true;

        }


        return $user->hasRole(
            'hospital_admin'
        );

    }




    public function update(
        User $user,
        Department $department
    ): bool
    {


        if($user->hasRole('platform_admin')){

            return true;

        }


        return $user->hospitals()
            ->where(
                'hospitals.id',
                $department->hospital_id
            )
            ->exists();

    }




    public function delete(
        User $user,
        Department $department
    ): bool
    {


        if($user->hasRole('platform_admin')){

            return true;

        }


        return $user->hospitals()
            ->where(
                'hospitals.id',
                $department->hospital_id
            )
            ->exists();

    }


}