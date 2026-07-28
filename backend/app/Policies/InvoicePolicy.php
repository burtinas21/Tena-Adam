<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;


class InvoicePolicy
{


    public function view(
        User $user,
        Invoice $invoice
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

            return $invoice
                ->payment
                ->hospital_id
                ==
                $user->hospital_id;

        }



        if(
            $user->hasRole('patient')
        ){

            return $invoice
                ->payment
                ->patient_id
                ==
                $user->patient->id;

        }


        return false;

    }



    public function create(User $user): bool
    {

        return $user->hasAnyRole([
            'hospital_admin',
            'platform_admin'
        ]);

    }



    public function update(User $user): bool
    {

        return $user->hasRole(
            'platform_admin'
        );

    }


    public function delete(User $user): bool
    {

        return $user->hasRole(
            'platform_admin'
        );

    }


}