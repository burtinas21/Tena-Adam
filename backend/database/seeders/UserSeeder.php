<?php

namespace Database\Seeders;


use App\Models\User;

use App\Models\Role;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;



class UserSeeder extends Seeder
{


    public function run(): void
    {


        // Create platform admin user

        $user = User::create([


            'id' => Str::uuid(),


            'first_name' => 'Platform',


            'last_name' => 'Admin',


            'email' => 'admin@gmail.com',


            'phone' => '0911111111',


            'password' => Hash::make('password'),


            'is_active' => true,


        ]);





        // Get platform_admin role

        $role = Role::where(
            'name',
            'platform_admin'
        )->first();





        // Attach role to user

        if($role){


            $user->roles()->attach(

                $role->id,

                [

                    'assigned_by'=>$user->id

                ]

            );


        }


    }


}