<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{

    public function viewAny(User $user): bool
    {
        return 
            $user->hasRole('platform_admin') ||
            $user->hasRole('hospital_admin') ||
            $user->hasRole('doctor');
    }


    public function view(
    User $user,
    Report $report
): bool
{

    if ($user->hasRole('platform_admin')) {

        return true;

    }


    if ($user->hasRole('hospital_admin')) {

        return $user->hospitalStaff()
            ->where(
                'hospital_id',
                $report->hospital_id
            )
            ->exists();

    }


    if ($user->hasRole('doctor')) {

        return $report->created_by === $user->id;

    }


    return false;

}



    public function create(User $user): bool
    {
        return 
            $user->hasRole('platform_admin') ||
            $user->hasRole('hospital_admin');
    }



    public function update(User $user, Report $report): bool
    {

        if ($user->hasRole('platform_admin')) {

            return true;

        }


        return $user->hasRole('hospital_admin')
            &&
            $report->created_by === $user->id;

    }



    public function delete(User $user, Report $report): bool
    {

        if ($user->hasRole('platform_admin')) {

            return true;

        }


        return $user->hasRole('hospital_admin')
            &&
            $report->created_by === $user->id;

    }


}