<?php

namespace Database\Seeders;


use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class HospitalAdminSeeder extends Seeder
{


    public function run(): void
    {


        $user = User::create([

            'id'=>Str::uuid(),

            'first_name'=>'Hospital',

            'last_name'=>'Admin',

            'email'=>'hospitalSeedadmin@gmail.com',

            'phone'=>'0922222222',

            'password'=>Hash::make('password'),

            'is_active'=>true,

        ]);



        $role = Role::where(
            'name',
            'hospital_admin'
        )->first();



        $user->roles()->attach(

            $role->id,

            [

                'assigned_by'=>null

            ]

        );


    }

}