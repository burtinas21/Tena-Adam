<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [

            [
                'name' => 'patient',
                'description' => 'Patient',
                'is_default' => true,
            ],

            [
                'name' => 'doctor',
                'description' => 'Healthcare Provider',
                'is_default' => false,
            ],

            [
                'name' => 'hospital_admin',
                'description' => 'Hospital Administrator',
                'is_default' => false,
            ],

            [
                'name' => 'platform_admin',
                'description' => 'Platform Administrator',
                'is_default' => false,
            ],

            [
                'name' => 'receptionist',
                'description' => 'Hospital Receptionist',
                'is_default' => false,
            ],

        ];

        foreach ($roles as $role) {

            Role::updateOrCreate(

                ['name' => $role['name'], 'hospital_id' => null],

                [
                    'id' => Str::uuid(),
                    'description' => $role['description'],
                    'is_default' => $role['is_default'],
                ]

            );

        }
    }
}
