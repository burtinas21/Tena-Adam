<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HealthcareProvider;

class HealthcareProviderPolicy
{

    public function viewAny(User $user): bool
    {
        // Platform admin, hospital admin, doctors, and patients can all list providers
        return $user->hasRole('platform_admin') ||
            $user->hasRole('hospital_admin') ||
            $user->hasRole('doctor') ||
            $user->hasRole('patient');
    }
  


    public function view(
        User $user,
        HealthcareProvider $provider
    ): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        // Patients can view any doctor profile (needed for browsing and booking)
        if ($user->hasRole('patient')) {
            return true;
        }

        return $user
            ->hospitals()
            ->where(
                'hospital_id',
                $provider->hospital_id
            )
            ->exists();

    }



    public function create(User $user): bool
    {

        return $user->hasRole('hospital_admin');

    }



   public function update(
User $user,
HealthcareProvider $provider
): bool
{


if(
$user->hasRole('doctor')
&&
$user->id === $provider->id
)
{

return true;

}


if(
$user->hasRole('hospital_admin')
)
{

return $user
->hospitalStaff()
->where(
'hospital_id',
$provider->hospital_id
)
->exists();


}


return false;


}



    public function delete(
        User $user,
        HealthcareProvider $provider
    ): bool
    {


        if($user->hasRole('platform_admin'))
        {
            return true;
        }


        return $user
            ->hospitals()
            ->where(
                'hospital_id',
                $provider->hospital_id
            )
            ->exists();

    }


}