<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function register(array $data)
    {

        $user = User::create([

            'first_name' => $data['first_name'],

            'last_name' => $data['last_name'],

            'email' => $data['email'],

            'phone' => $data['phone'] ?? null,

            'password' => Hash::make(
                $data['password']
            ),

        ]);

        /*
        Assign Patient Role
        */

        $patientRole = Role::where(
            'name',
            'patient'
        )->first();

        $user->roles()->attach(
            $patientRole->id
        );


        Patient::create([

            'id' => $user->id,

            'date_of_birth' => $data['date_of_birth'],

            'gender' => $data['gender'],

            'patient_status' => 'pending',

            'registered_by' => $user->id,

        ]);

        return $user;

    }

    public function login(array $data)
{
    if (! Auth::attempt($data)) {
        return null;
    }

    $user = Auth::user();

    $user->update([
        'last_login' => now(),
    ]);

    // Load roles relationship
    $user->load('roles');

    // For doctors, also load their healthcare provider profile
    // so the frontend can display their profile picture in the navbar
    if ($user->hasRole('doctor')) {
        $user->load('healthcareProvider');
    }

    // For hospital admins and receptionists, load their hospital(s)
    // so the frontend can read hospital_id directly from the user object
    if ($user->hasRole('hospital_admin') || $user->hasRole('receptionist')) {
        $user->load('hospitalStaff');
        $user->load('hospitals');
    }

    return $user;
}

    public function logout($user)
    {

        $user->tokens()->delete();

    }
}
