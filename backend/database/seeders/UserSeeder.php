<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => Str::uuid(),

            'first_name' => 'Platform',

            'last_name' => 'Admin',

            'email' => 'admin@gmail.com',

            'phone' => '0911111111',

            'password' => Hash::make('password'),

            'role' => 'platform_admin',

            'is_active' => true,
        ]);
    }
}