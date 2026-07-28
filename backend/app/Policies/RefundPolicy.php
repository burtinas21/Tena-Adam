<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Refund;


class RefundPolicy
{


    public function create(User $user): bool
    {

        return $user->hasAnyRole([
            'patient',
            'hospital_admin'
        ]);

    }




    public function view(
        User $user,
        Refund $refund
    ): bool
    {

        if(
            $user->hasRole('platform_admin')
        ){

            return true;

        }



        if(
            $user->hasRole('hospital_admin')
        ){

            return $refund
                ->payment
                ->hospital_id
                ==
                $user->hospital_id;

        }



        return false;

    }




    public function approve(User $user): bool
    {

        return $user->hasAnyRole([
            'platform_admin',
            'hospital_admin'
        ]);

    }




    public function delete(User $user): bool
    {

        return $user->hasRole(
            'platform_admin'
        );

    }


}