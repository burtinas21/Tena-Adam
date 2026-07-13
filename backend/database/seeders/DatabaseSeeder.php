<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Roles & permissions (no FK dependencies)
            RoleSeeder::class,
            PermissionSeeder::class,

            // 2. Platform admin user
            UserSeeder::class,

            // 3. Assign permissions to roles
            RolePermissionSeeder::class,

            // 4. All Ethiopia hospital data:
            //    hospitals → departments → facilities → operating hours
            //    → hospital admins → doctors → schedules → leaves
            //    → patients → appointments → slots → queue
            EthiopiaHospitalSeeder::class,
        ]);
    }
}
