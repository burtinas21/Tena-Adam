<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::insert([

            [
                'id' => Str::uuid(),
                'name' => 'manage_users',
                'module' => 'users'
            ],

            [
                'id' => Str::uuid(),
                'name' => 'manage_hospitals',
                'module' => 'hospitals'
            ],

            [
                'id' => Str::uuid(),
                'name' => 'manage_doctors',
                'module' => 'doctors'
            ],

            [
                'id' => Str::uuid(),
                'name' => 'manage_appointments',
                'module' => 'appointments'
            ],

            [
                'id' => Str::uuid(),
                'name' => 'view_reports',
                'module' => 'reports'
            ]
        ]);
    }
}