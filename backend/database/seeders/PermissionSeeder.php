<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Authentication
            ['register',         'Authentication'],
            ['login',            'Authentication'],
            ['logout',           'Authentication'],
            ['forgot_password',  'Authentication'],
            ['reset_password',   'Authentication'],

            // Users
            ['view_users',   'Users'],
            ['create_users', 'Users'],
            ['update_users', 'Users'],
            ['delete_users', 'Users'],

            // Roles
            ['view_roles',   'Roles'],
            ['create_roles', 'Roles'],
            ['update_roles', 'Roles'],
            ['delete_roles', 'Roles'],

            // Permissions
            ['view_permissions',   'Permissions'],
            ['assign_permissions', 'Permissions'],

            // Hospitals
            ['view_hospitals',   'Hospital'],
            ['create_hospitals', 'Hospital'],
            ['update_hospitals', 'Hospital'],
            ['delete_hospitals', 'Hospital'],

            // Departments
            ['view_departments',   'Department'],
            ['create_departments', 'Department'],
            ['update_departments', 'Department'],
            ['delete_departments', 'Department'],

            // Facilities
            ['view_facilities',   'Facility'],
            ['create_facilities', 'Facility'],
            ['update_facilities', 'Facility'],
            ['delete_facilities', 'Facility'],

            // Operating Hours
            ['view_operating_hours',   'Hospital'],
            ['create_operating_hours', 'Hospital'],
            ['update_operating_hours', 'Hospital'],
            ['delete_operating_hours', 'Hospital'],

            // Doctors
            ['view_doctors',   'Doctor'],
            ['create_doctors', 'Doctor'],
            ['update_doctors', 'Doctor'],
            ['delete_doctors', 'Doctor'],
            ['manage_schedule', 'Doctor'],
            ['manage_leave',    'Doctor'],

            // Patients
            ['view_patients',   'Patient'],
            ['create_patients', 'Patient'],
            ['update_patients', 'Patient'],
            ['delete_patients', 'Patient'],

            // Appointment
            ['book_appointment',        'Appointment'],
            ['approve_appointment',     'Appointment'],
            ['cancel_appointment',      'Appointment'],
            ['reschedule_appointment',  'Appointment'],
            ['view_appointments',       'Appointment'],

            // Queue
            ['view_queue',       'Queue'],
            ['manage_queue',     'Queue'],
            ['call_next_patient','Queue'],

            // EMR
            ['view_emr',          'EMR'],
            ['create_emr',        'EMR'],
            ['update_emr',        'EMR'],
            ['create_prescription','EMR'],
            ['view_prescription', 'EMR'],

            // Telehealth
            ['create_telehealth_session', 'Telehealth'],
            ['join_telehealth_session',   'Telehealth'],

            // Symptoms
            ['use_symptom_checker', 'Symptom'],
            ['manage_symptoms',     'Symptom'],

            // Notification
            ['send_notifications', 'Notification'],
            ['view_notifications', 'Notification'],

            // Reports
            ['view_reports',   'Reports'],
            ['export_reports', 'Reports'],

            // Audit
            ['view_audit_logs', 'Audit'],

        ];

        foreach ($permissions as $permission) {

            Permission::updateOrCreate(

                ['name' => $permission[0]],

                [
                    'id'          => Str::uuid(),
                    'name'        => $permission[0],
                    'module'      => $permission[1],
                    'description' => ucfirst(str_replace('_', ' ', $permission[0])),
                ]

            );

        }
    }
}
