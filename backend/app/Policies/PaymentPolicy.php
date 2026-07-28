<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Payment;

class PaymentPolicy
{

    /**
     * View payment list
     */
    public function viewAny(User $user): bool
    {

        return $user->hasAnyRole([
            'platform_admin',
            'hospital_admin',
            'receptionist'
        ]);

    }



    /**
     * View single payment
     */
    public function view(
        User $user,
        Payment $payment
    ): bool
    {

        // Platform admin can see everything
        if($user->hasRole('platform_admin')){
            return true;
        }


        // Hospital admin sees own hospital payments
        if($user->hasRole('hospital_admin')){

            return $user->hospitals()
                ->where(
                    'hospitals.id',
                    $payment->hospital_id
                )
                ->exists();

        }


        // Patient sees own payment
        if($user->hasRole('patient')){

            return $payment->patient_id 
                === 
                $user->patient->id;

        }


        return false;

    }



    /**
     * Create payment
     */
    public function create(User $user): bool
    {

        return $user->hasAnyRole([
            'hospital_admin',
            'receptionist'
        ]);

    }



    /**
     * Update payment
     */
    public function update(
        User $user,
        Payment $payment
    ): bool
    {

        return $user->hasAnyRole([
            'platform_admin',
            'hospital_admin'
        ]);

    }



    /**
     * Delete payment
     */
    public function delete(
        User $user,
        Payment $payment
    ): bool
    {

        return $user->hasRole(
            'platform_admin'
        );

    }

}