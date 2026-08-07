<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TranslationKey;


class TranslationKeySeeder extends Seeder
{

    public function run(): void
    {
        $keys = [

            // ── Dashboard ────────────────────────────────────────────────
            ['key' => 'dashboard.title',                'module' => 'dashboard',    'description' => 'Dashboard page title'],
            ['key' => 'dashboard.overview',             'module' => 'dashboard',    'description' => 'Dashboard overview heading'],
            ['key' => 'dashboard.subtitle',             'module' => 'dashboard',    'description' => 'Dashboard subtitle'],
            ['key' => 'dashboard.refresh',              'module' => 'dashboard',    'description' => 'Refresh button'],
            ['key' => 'dashboard.export_report',        'module' => 'dashboard',    'description' => 'Export report button'],
            ['key' => 'dashboard.total_patients',       'module' => 'dashboard',    'description' => 'Total patients stat label'],
            ['key' => 'dashboard.total_doctors',        'module' => 'dashboard',    'description' => 'Total doctors stat label'],
            ['key' => 'dashboard.departments',          'module' => 'dashboard',    'description' => 'Departments stat label'],
            ['key' => 'dashboard.total_appointments',   'module' => 'dashboard',    'description' => 'Total appointments stat label'],
            ['key' => 'dashboard.completed',            'module' => 'dashboard',    'description' => 'Completed stat label'],
            ['key' => 'dashboard.pending',              'module' => 'dashboard',    'description' => 'Pending stat label'],
            ['key' => 'dashboard.cancelled',            'module' => 'dashboard',    'description' => 'Cancelled stat label'],
            ['key' => 'dashboard.active_telemed',       'module' => 'dashboard',    'description' => 'Active telemedicine stat label'],
            ['key' => 'dashboard.new_patients',         'module' => 'dashboard',    'description' => 'New patients stat label'],
            ['key' => 'dashboard.this_month',           'module' => 'dashboard',    'description' => 'This month trend label'],
            ['key' => 'dashboard.appointment_overview', 'module' => 'dashboard',    'description' => 'Appointment overview chart title'],
            ['key' => 'dashboard.welcome',              'module' => 'dashboard',    'description' => 'Welcome greeting'],
            ['key' => 'dashboard.upcoming',             'module' => 'dashboard',    'description' => 'Upcoming appointments label'],
            ['key' => 'dashboard.active_rx',            'module' => 'dashboard',    'description' => 'Active prescriptions label'],
            ['key' => 'dashboard.records',              'module' => 'dashboard',    'description' => 'Medical records label'],
            ['key' => 'dashboard.retry',                'module' => 'dashboard',    'description' => 'Retry button label'],

            // ── Quick Actions ────────────────────────────────────────────
            ['key' => 'action.book_appointment',    'module' => 'actions',  'description' => 'Book appointment quick action'],
            ['key' => 'action.join_telemedicine',   'module' => 'actions',  'description' => 'Join telemedicine quick action'],
            ['key' => 'action.search_doctors',      'module' => 'actions',  'description' => 'Search doctors quick action'],
            ['key' => 'action.view_records',        'module' => 'actions',  'description' => 'View records quick action'],

            // ── Common Buttons ───────────────────────────────────────────
            ['key' => 'button.save',                'module' => 'common',   'description' => 'Save button'],
            ['key' => 'button.cancel',              'module' => 'common',   'description' => 'Cancel button'],
            ['key' => 'button.submit',              'module' => 'common',   'description' => 'Submit button'],
            ['key' => 'button.delete',              'module' => 'common',   'description' => 'Delete button'],
            ['key' => 'button.edit',                'module' => 'common',   'description' => 'Edit button'],
            ['key' => 'button.add',                 'module' => 'common',   'description' => 'Add button'],
            ['key' => 'button.close',               'module' => 'common',   'description' => 'Close button'],
            ['key' => 'button.confirm',             'module' => 'common',   'description' => 'Confirm button'],
            ['key' => 'button.back',                'module' => 'common',   'description' => 'Back button'],
            ['key' => 'button.search',              'module' => 'common',   'description' => 'Search button'],
            ['key' => 'button.filter',              'module' => 'common',   'description' => 'Filter button'],
            ['key' => 'button.export',              'module' => 'common',   'description' => 'Export button'],
            ['key' => 'button.loading',             'module' => 'common',   'description' => 'Loading state text'],
            ['key' => 'button.view_all',            'module' => 'common',   'description' => 'View all button'],
            ['key' => 'button.mark_all_read',       'module' => 'common',   'description' => 'Mark all read button'],

            // ── Authentication ───────────────────────────────────────────
            ['key' => 'login',                          'module' => 'auth', 'description' => 'Login text'],
            ['key' => 'logout',                         'module' => 'auth', 'description' => 'Logout text'],
            ['key' => 'auth.sign_in',                   'module' => 'auth', 'description' => 'Sign In heading'],
            ['key' => 'auth.sign_in_subtitle',          'module' => 'auth', 'description' => 'Sign in subtitle'],
            ['key' => 'auth.email',                     'module' => 'auth', 'description' => 'Email address label'],
            ['key' => 'auth.password',                  'module' => 'auth', 'description' => 'Password label'],
            ['key' => 'auth.remember_me',               'module' => 'auth', 'description' => 'Remember me checkbox'],
            ['key' => 'auth.forgot_password',           'module' => 'auth', 'description' => 'Forgot password link'],
            ['key' => 'auth.no_account',                'module' => 'auth', 'description' => 'No account prompt'],
            ['key' => 'auth.create_account',            'module' => 'auth', 'description' => 'Create account link'],
            ['key' => 'auth.secure_auth',               'module' => 'auth', 'description' => 'Secure authentication badge'],
            ['key' => 'auth.login_success',             'module' => 'auth', 'description' => 'Login success message'],
            ['key' => 'auth.invalid_credentials',       'module' => 'auth', 'description' => 'Invalid credentials error'],

            // ── Sidebar navigation ───────────────────────────────────────
            ['key' => 'nav.dashboard',              'module' => 'nav', 'description' => 'Sidebar: Dashboard'],
            ['key' => 'nav.departments',            'module' => 'nav', 'description' => 'Sidebar: Departments'],
            ['key' => 'nav.facilities',             'module' => 'nav', 'description' => 'Sidebar: Facilities'],
            ['key' => 'nav.operating_hours',        'module' => 'nav', 'description' => 'Sidebar: Operating Hours'],
            ['key' => 'nav.doctors_staff',          'module' => 'nav', 'description' => 'Sidebar: Doctors & Staff'],
            ['key' => 'nav.appointments',           'module' => 'nav', 'description' => 'Sidebar: Appointments'],
            ['key' => 'nav.queue_management',       'module' => 'nav', 'description' => 'Sidebar: Queue Management'],
            ['key' => 'nav.telemedicine',           'module' => 'nav', 'description' => 'Sidebar: Telemedicine'],
            ['key' => 'nav.doctor_leaves',          'module' => 'nav', 'description' => 'Sidebar: Doctor Leaves'],
            ['key' => 'nav.notifications',          'module' => 'nav', 'description' => 'Sidebar: Notifications'],
            ['key' => 'nav.settings',               'module' => 'nav', 'description' => 'Sidebar: Settings'],
            ['key' => 'nav.reports_analytics',      'module' => 'nav', 'description' => 'Sidebar: Reports & Analytics'],
            ['key' => 'nav.symptoms',               'module' => 'nav', 'description' => 'Sidebar: Symptoms'],
            ['key' => 'nav.schedule',               'module' => 'nav', 'description' => 'Sidebar: Schedule'],
            ['key' => 'nav.queue',                  'module' => 'nav', 'description' => 'Sidebar: Queue'],
            ['key' => 'nav.medical_encounter',      'module' => 'nav', 'description' => 'Sidebar: Medical Encounter'],
            ['key' => 'nav.vitals',                 'module' => 'nav', 'description' => 'Sidebar: Vitals'],
            ['key' => 'nav.prescriptions',          'module' => 'nav', 'description' => 'Sidebar: Prescriptions'],
            ['key' => 'nav.documents',              'module' => 'nav', 'description' => 'Sidebar: Documents'],
            ['key' => 'nav.profile',                'module' => 'nav', 'description' => 'Sidebar: Profile'],
            ['key' => 'nav.hospitals',              'module' => 'nav', 'description' => 'Sidebar: Hospitals'],
            ['key' => 'nav.doctors',                'module' => 'nav', 'description' => 'Sidebar: Doctors'],
            ['key' => 'nav.telehealth',             'module' => 'nav', 'description' => 'Sidebar: TeleHealth'],
            ['key' => 'nav.symptom_checker',        'module' => 'nav', 'description' => 'Sidebar: Symptom Checker'],
            ['key' => 'nav.medical_history',        'module' => 'nav', 'description' => 'Sidebar: Medical History'],
            ['key' => 'nav.hospital_network',       'module' => 'nav', 'description' => 'Sidebar: Hospital Network'],
            ['key' => 'nav.hospital_admins',        'module' => 'nav', 'description' => 'Sidebar: Hospital Admins'],
            ['key' => 'nav.analytics',              'module' => 'nav', 'description' => 'Sidebar: Analytics'],
            ['key' => 'nav.audit_logs',             'module' => 'nav', 'description' => 'Sidebar: Audit Logs'],
            ['key' => 'nav.reports',                'module' => 'nav', 'description' => 'Sidebar: Reports'],
            ['key' => 'nav.registration',           'module' => 'nav', 'description' => 'Sidebar: Registration'],
            ['key' => 'nav.notification',           'module' => 'nav', 'description' => 'Sidebar: Notification'],
            ['key' => 'nav.roles_permissions',      'module' => 'nav', 'description' => 'Sidebar: Roles & Permissions'],

            // ── Roles & Permissions page ─────────────────────────────────
            ['key' => 'roles.page_title',                   'module' => 'roles', 'description' => 'Roles page heading'],
            ['key' => 'roles.page_subtitle_platform',       'module' => 'roles', 'description' => 'Roles page subtitle for platform admin'],
            ['key' => 'roles.page_subtitle_hospital',       'module' => 'roles', 'description' => 'Roles page subtitle for hospital admin'],
            ['key' => 'roles.new_role',                     'module' => 'roles', 'description' => 'New Role button'],
            ['key' => 'roles.hospital_notice',              'module' => 'roles', 'description' => 'Hospital roles info notice'],
            ['key' => 'roles.permissions_count',            'module' => 'roles', 'description' => 'X permissions label'],
            ['key' => 'roles.no_roles',                     'module' => 'roles', 'description' => 'Empty state: no roles found'],
            ['key' => 'roles.system_badge',                 'module' => 'roles', 'description' => 'SYSTEM badge on role card'],
            ['key' => 'roles.manage_permissions',           'module' => 'roles', 'description' => 'Manage Permissions button/heading'],
            ['key' => 'roles.view_details',                 'module' => 'roles', 'description' => 'View Details menu item'],
            ['key' => 'roles.delete',                       'module' => 'roles', 'description' => 'Delete menu item'],
            ['key' => 'roles.restricted_badge',             'module' => 'roles', 'description' => 'RESTRICTED badge on permission'],
            ['key' => 'roles.select_all',                   'module' => 'roles', 'description' => 'Select all button'],
            ['key' => 'roles.deselect_all',                 'module' => 'roles', 'description' => 'Deselect all button'],
            ['key' => 'roles.selected_count',               'module' => 'roles', 'description' => 'X of Y selected label'],
            ['key' => 'roles.search_placeholder',           'module' => 'roles', 'description' => 'Search permissions placeholder'],
            ['key' => 'roles.no_permissions_found',         'module' => 'roles', 'description' => 'No permissions found message'],
            ['key' => 'roles.try_different_search',         'module' => 'roles', 'description' => 'Try different search hint'],
            ['key' => 'roles.saving',                       'module' => 'roles', 'description' => 'Auto-saving indicator'],
            ['key' => 'roles.saved',                        'module' => 'roles', 'description' => 'Saved indicator'],
            ['key' => 'roles.close',                        'module' => 'roles', 'description' => 'Close button'],
            ['key' => 'roles.cancel',                       'module' => 'roles', 'description' => 'Cancel button'],
            ['key' => 'roles.role_details',                 'module' => 'roles', 'description' => 'Role Details modal heading'],
            ['key' => 'roles.description_label',            'module' => 'roles', 'description' => 'Description field label'],
            ['key' => 'roles.total_permissions',            'module' => 'roles', 'description' => 'Total Permissions label'],
            ['key' => 'roles.type_label',                   'module' => 'roles', 'description' => 'Type field label'],
            ['key' => 'roles.system_role',                  'module' => 'roles', 'description' => 'System Role type value'],
            ['key' => 'roles.custom_role',                  'module' => 'roles', 'description' => 'Custom Role type value'],
            ['key' => 'roles.edit_role',                    'module' => 'roles', 'description' => 'Edit Role modal heading'],
            ['key' => 'roles.role_name_label',              'module' => 'roles', 'description' => 'Role Name input label'],
            ['key' => 'roles.role_name_placeholder',        'module' => 'roles', 'description' => 'Role Name input placeholder'],
            ['key' => 'roles.description_placeholder',      'module' => 'roles', 'description' => 'Description input placeholder'],
            ['key' => 'roles.save_changes',                 'module' => 'roles', 'description' => 'Save Changes button'],
            ['key' => 'roles.create_role',                  'module' => 'roles', 'description' => 'Create Role button'],
            ['key' => 'roles.delete_role_title',            'module' => 'roles', 'description' => 'Delete Role modal heading'],
            ['key' => 'roles.delete_irreversible',          'module' => 'roles', 'description' => 'Delete irreversible warning'],
            ['key' => 'roles.delete_confirm_text',          'module' => 'roles', 'description' => 'Delete confirmation text'],
            ['key' => 'roles.permissions_enabled',          'module' => 'roles', 'description' => 'X of Y permissions enabled footer'],

            // ── Healthcare entities ──────────────────────────────────────
            ['key' => 'patient',                    'module' => 'patient',      'description' => 'Patient label'],
            ['key' => 'doctor',                     'module' => 'doctor',       'description' => 'Doctor label'],
            ['key' => 'appointment',                'module' => 'appointment',  'description' => 'Appointment label'],
            ['key' => 'telehealth',                 'module' => 'telehealth',   'description' => 'Telehealth label'],
            ['key' => 'hospital',                   'module' => 'hospital',     'description' => 'Hospital label'],
            ['key' => 'department',                 'module' => 'department',   'description' => 'Department label'],
            ['key' => 'facility',                   'module' => 'facility',     'description' => 'Facility label'],
            ['key' => 'prescription',               'module' => 'prescription', 'description' => 'Prescription label'],
            ['key' => 'queue',                      'module' => 'queue',        'description' => 'Queue label'],
            ['key' => 'schedule',                   'module' => 'schedule',     'description' => 'Schedule label'],
            ['key' => 'leave',                      'module' => 'leave',        'description' => 'Leave label'],
            ['key' => 'vital',                      'module' => 'vital',        'description' => 'Vital label'],

            // ── Appointment Status ───────────────────────────────────────
            ['key' => 'status.pending',             'module' => 'status',   'description' => 'Pending status'],
            ['key' => 'status.confirmed',           'module' => 'status',   'description' => 'Confirmed status'],
            ['key' => 'status.completed',           'module' => 'status',   'description' => 'Completed status'],
            ['key' => 'status.cancelled',           'module' => 'status',   'description' => 'Cancelled status'],
            ['key' => 'status.no_show',             'module' => 'status',   'description' => 'No show status'],
            ['key' => 'status.active',              'module' => 'status',   'description' => 'Active status'],
            ['key' => 'status.inactive',            'module' => 'status',   'description' => 'Inactive status'],
            ['key' => 'status.approved',            'module' => 'status',   'description' => 'Approved status'],
            ['key' => 'status.rejected',            'module' => 'status',   'description' => 'Rejected status'],

            // ── Notifications ────────────────────────────────────────────
            ['key' => 'notification.title',         'module' => 'notification', 'description' => 'Notifications panel title'],
            ['key' => 'notification.empty',         'module' => 'notification', 'description' => 'No notifications message'],
            ['key' => 'notification.view_all',      'module' => 'notification', 'description' => 'View all notifications link'],
            ['key' => 'notification.just_now',      'module' => 'notification', 'description' => 'Just now time label'],

            // ── Search ───────────────────────────────────────────────────
            ['key' => 'search.placeholder',            'module' => 'search',   'description' => 'Search placeholder'],
            ['key' => 'search.doctors',                'module' => 'search',   'description' => 'Search doctors placeholder'],
            ['key' => 'search.hospitals',              'module' => 'search',   'description' => 'Search hospitals placeholder'],
            ['key' => 'search.appointments',           'module' => 'search',   'description' => 'Search appointments placeholder'],

            // ── Reschedule ───────────────────────────────────────────────
            ['key' => 'reschedule.title',           'module' => 'appointment',  'description' => 'Reschedule modal title'],
            ['key' => 'reschedule.subtitle',        'module' => 'appointment',  'description' => 'Reschedule modal subtitle'],
            ['key' => 'reschedule.new_date',        'module' => 'appointment',  'description' => 'New date label'],
            ['key' => 'reschedule.available_slots', 'module' => 'appointment',  'description' => 'Available slots label'],
            ['key' => 'reschedule.no_slots',        'module' => 'appointment',  'description' => 'No slots available message'],
            ['key' => 'reschedule.loading_slots',   'module' => 'appointment',  'description' => 'Loading slots message'],
            ['key' => 'reschedule.confirm',         'module' => 'appointment',  'description' => 'Confirm reschedule button'],

            // ── Table / List common ──────────────────────────────────────
            ['key' => 'table.no_data',              'module' => 'common',   'description' => 'No data in table'],
            ['key' => 'table.loading',              'module' => 'common',   'description' => 'Table loading'],
            ['key' => 'table.actions',              'module' => 'common',   'description' => 'Actions column header'],
            ['key' => 'table.name',                 'module' => 'common',   'description' => 'Name column header'],
            ['key' => 'table.email',                'module' => 'common',   'description' => 'Email column header'],
            ['key' => 'table.phone',                'module' => 'common',   'description' => 'Phone column header'],
            ['key' => 'table.status',               'module' => 'common',   'description' => 'Status column header'],
            ['key' => 'table.date',                 'module' => 'common',   'description' => 'Date column header'],

            // ── Days of week ─────────────────────────────────────────────
            ['key' => 'day.monday',     'module' => 'common', 'description' => 'Monday'],
            ['key' => 'day.tuesday',    'module' => 'common', 'description' => 'Tuesday'],
            ['key' => 'day.wednesday',  'module' => 'common', 'description' => 'Wednesday'],
            ['key' => 'day.thursday',   'module' => 'common', 'description' => 'Thursday'],
            ['key' => 'day.friday',     'module' => 'common', 'description' => 'Friday'],
            ['key' => 'day.saturday',   'module' => 'common', 'description' => 'Saturday'],
            ['key' => 'day.sunday',     'module' => 'common', 'description' => 'Sunday'],

            // ── Error / empty states ─────────────────────────────────────
            ['key' => 'error.generic',          'module' => 'common',   'description' => 'Generic error message'],
            ['key' => 'error.not_found',        'module' => 'common',   'description' => 'Not found error'],
            ['key' => 'error.unauthorized',     'module' => 'common',   'description' => 'Unauthorized error'],
            ['key' => 'empty.no_appointments',  'module' => 'common',   'description' => 'No appointments message'],
            ['key' => 'empty.no_results',       'module' => 'common',   'description' => 'No results found message'],

        ];

        foreach ($keys as $key) {
            TranslationKey::updateOrCreate(
                ['key' => $key['key']],
                $key
            );
        }
    }
}
