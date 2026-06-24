<?php

namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthService
{


    public function register(array $data)
    {


        $user = User::create([

            'first_name' => $data['first_name'],

            'last_name' => $data['last_name'],

            'email' => $data['email'],

            'phone' => $data['phone'] ?? null,

            'role' => 'patient',

            'password' => Hash::make($data['password'])

        ]);



        $token = $user
            ->createToken('smart-care-token')
            ->plainTextToken;



        return [

            'user'=>$user,

            'token'=>$token

        ];

    }





    public function login(array $data)
    {


        $user = User::where(
            'email',
            $data['email']
        )->first();



        if(!$user)
        {

            return null;

        }



        if(!Hash::check(
            $data['password'],
            $user->password
        ))

        {

            return null;

        }



        $token = $user
            ->createToken('smart-care-token')
            ->plainTextToken;



        return [

            'user'=>$user,

            'token'=>$token

        ];


    }


}