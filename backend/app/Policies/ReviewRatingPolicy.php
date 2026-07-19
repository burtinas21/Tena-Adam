<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReviewRating;

class ReviewRatingPolicy
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
        ReviewRating $review
    ): bool {

        if ($user->hasRole('platform_admin')) {
            return true;
        }


        if ($user->hasRole('doctor')) {

            return $review->doctor_id === $user->id;

        }


        if ($user->hasRole('patient')) {

            return $review->patient_id === $user->id;

        }


        return $user->hasRole('hospital_admin');
    }


    public function create(User $user): bool
    {
        return $user->hasRole('patient');
    }


    public function update(
        User $user,
        ReviewRating $review
    ): bool {

        return
            $user->hasRole('patient')
            &&
            $review->patient_id === $user->id;

    }


    public function delete(
        User $user,
        ReviewRating $review
    ): bool {

        return
            $user->hasRole('patient')
            &&
            $review->patient_id === $user->id;

    }

}