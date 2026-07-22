<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        // Get the platform admin user (or first user as fallback)
        $admin = User::whereHas('roles', fn($q) => $q->where('name', 'platform_admin'))->first()
            ?? User::first();

        if (! $admin) {
            $this->command->warn('No users found — skipping AuditLogSeeder.');
            return;
        }

        $sampleLogs = [
            ['Created Hospitals',      'hospitals',            '192.168.1.10'],
            ['Updated Hospitals',      'hospitals',            '192.168.1.10'],
            ['Created Healthcare Providers', 'healthcare_providers', '10.0.0.5'],
            ['Updated Departments',    'departments',          '10.0.0.5'],
            ['Created Departments',    'departments',          '192.168.1.22'],
            ['Deleted Hospitals',      'hospitals',            '203.0.113.12'],
            ['Login',                  'auth',                 '192.168.1.10'],
            ['Logout',                 'auth',                 '192.168.1.10'],
            ['Registered',             'users',                '203.0.113.55'],
            ['Approved Doctor Leaves', 'doctor_leaves',        '10.0.4.11'],
            ['Created Appointments',   'appointments',         '192.168.1.30'],
            ['Updated Appointments',   'appointments',         '192.168.1.30'],
            ['Cancelled Appointments', 'appointments',         '192.168.1.45'],
            ['Created Facilities',     'facilities',           '10.0.0.8'],
            ['Updated Users',          'users',                '192.168.1.10'],
        ];

        foreach ($sampleLogs as [$action, $table, $ip]) {
            AuditLog::create([
                'id'           => Str::uuid(),
                'user_id'      => $admin->id,
                'action'       => $action,
                'target_table' => $table,
                'target_id'    => Str::uuid(),
                'details'      => ['method' => 'POST', 'path' => "api/{$table}", 'status' => 201],
                'ip_address'   => $ip,
                'user_agent'   => 'Mozilla/5.0 (Seeder)',
                'created_at'   => now()->subMinutes(rand(1, 10000)),
            ]);
        }

        $this->command->info('AuditLogSeeder: seeded ' . count($sampleLogs) . ' audit log entries.');
    }
}
