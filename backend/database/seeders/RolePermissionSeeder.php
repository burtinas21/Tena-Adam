<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {

        $patient = Role::where('name', 'patient')->first();

        $patientPermissions = [

            'view_hospitals',
            'view_departments',

            'view_doctors',

            'book_appointment',
            'cancel_appointment',
            'reschedule_appointment',
            'view_appointments',

            'view_queue',

            'view_emr',

            'view_prescription',

            'join_telehealth_session',

            'use_symptom_checker',

            'view_notifications',

        ];

        foreach ($patientPermissions as $permission) {

            $patient
                ->permissions()
                ->syncWithoutDetaching(

                    Permission::where('name', $permission)->first()

                );

        }

        $doctor = Role::where('name', 'doctor')->first();

        $doctorPermissions = [

            'view_patients',

            'view_appointments',

            'approve_appointment',

            'cancel_appointment',

            'manage_schedule',

            'manage_leave',

            'view_queue',

            'manage_queue',

            'call_next_patient',

            'view_emr',

            'create_emr',

            'update_emr',

            'create_prescription',

            'view_prescription',

            'create_telehealth_session',

            'join_telehealth_session',

            'view_notifications',

            'view_departments',   // needed for referral modal doctor/department picker

            'view_doctors',       // needed for referral modal doctor list

        ];

        foreach ($doctorPermissions as $permission) {

            $doctor
                ->permissions()
                ->syncWithoutDetaching(

                    Permission::where('name', $permission)->first()

                );

        }

        $hospitalAdmin = Role::where('name', 'hospital_admin')->first();

        $hospitalAdminPermissions = [

            'view_users',
            'create_users',
            'update_users',
            'delete_users',

            'view_patients',
            'create_patients',
            'update_patients',

            'view_doctors',

            'create_doctors',

            'update_doctors',

            'view_departments',

            'create_departments',

            'update_departments',

            'view_appointments',
            'delete_departments',

            'manage_queue',

            'view_reports',
            'export_reports',

            'send_notifications',
            'view_facilities' ,
            'create_facilities',
            'update_facilities',
            'delete_facilities',
            'view_operating_hours' ,
            'create_operating_hours',
            'update_operating_hours',
            'delete_operating_hours',
            'view_hospitals'
        ];

        foreach ($hospitalAdminPermissions as $permission) {

            $hospitalAdmin
                ->permissions()
                ->syncWithoutDetaching(

                    Permission::where('name', $permission)->first()

                );

        }

        $platformAdmin = Role::where('name', 'platform_admin')->first();

        $allPermissions = Permission::all();

        $platformAdmin
            ->permissions()
            ->syncWithoutDetaching($allPermissions);

        // ----------------------------------------------------------------
        // Receptionist
        $receptionist = Role::where('name', 'receptionist')->first();

        if ($receptionist) {

            $receptionistPermissions = [
                'create_patients',   // register patients
                'view_patients',     // view patient records
                'manage_queue',      // generate queue & add walk-ins
                'view_queue',        // view queue
                'call_next_patient', // call next in queue
                'view_appointments', // view appointments
                'view_hospitals',    // view hospital info
                'view_departments',  // view departments
            ];

            foreach ($receptionistPermissions as $permission) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $receptionist->permissions()->syncWithoutDetaching($perm);
                }
            }

        }

    }
}
