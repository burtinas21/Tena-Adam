<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([

            [
                'id' => Str::uuid(),
                'name' => 'platform_admin',
                'description' => 'Full platform access',
                'is_default' => false,
            ],

            [
                'id' => Str::uuid(),
                'name' => 'hospital_admin',
                'description' => 'Hospital management',
                'is_default' => false,
            ],

            [
                'id' => Str::uuid(),
                'name' => 'doctor',
                'description' => 'Doctor role',
                'is_default' => false,
            ],

            [
                'id' => Str::uuid(),
                'name' => 'patient',
                'description' => 'Patient role',
                'is_default' => true,
            ]
        ]);
    }
}