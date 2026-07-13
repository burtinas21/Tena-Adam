<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{


    public function view(User $user,Department $department ): bool
    {


        if ($user->hasRole('platform_admin')) {

            return true;

        }


        return $user->hospitals()
            ->where(
                'hospital_id',
                $department->hospital_id
            )
            ->exists();

    }




    public function create(User $user): bool
    {

        return $user->hasRole(
            'hospital_admin'
        );

    }





    public function update(  User $user,Department $department): bool
    {


        if ($user->hasRole('platform_admin')) {

            return true;

        }


        return $user->hospitals()
            ->where(
                'hospital_id',
                $department->hospital_id
            )
            ->exists();

    }





    public function delete(  User $user, Department $department): bool
    {


        return $this->update(
            $user,
            $department
        );

    }

}