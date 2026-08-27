<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\PasswordResetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\FacilityController;
use App\Http\Controllers\API\HospitalController;
use App\Http\Controllers\API\HospitalOperatingHourController;
use App\Http\Controllers\API\HospitalStaffController;
use App\Http\Controllers\Api\HealthcareProviderController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\Api\DoctorLeaveController;
use App\Http\Controllers\Api\AppointmentSlotController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\PatientProfileController;
use App\Http\Controllers\Api\ReceptionistPatientController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\MedicalEncounterController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\VitalController;
use App\Http\Controllers\Api\TelehealthSessionController;
use App\Http\Controllers\Api\TelehealthAttendanceController;
use App\Http\Controllers\Api\SymptomDepartmentMappingController;
// use App\Http\Controllers\Api\SymptomAnalyticController;
use App\Http\Controllers\Api\SymptomAnalyticsController;
use App\Http\Controllers\Api\SymptomController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\Api\NotificationTemplateController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewRatingController;
use App\Http\Controllers\Api\MedicalDocumentController;
use App\Http\Controllers\Api\GoogleAuthController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\RefundController;
Route::post('payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');
Route::post('payments/webhook',[PaymentController::class, 'webhook'])->name('payments.webhook');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('payments/by-appointment', [PaymentController::class, 'byAppointment']);
    Route::post('payments/{payment}/reinitialize', [PaymentController::class, 'reinitialize']);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});
Route::apiResource('payments', PaymentController::class);
Route::get('invoices', [InvoiceController::class, 'index']);
Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
Route::get('refunds', [RefundController::class, 'index']);
Route::post('refunds', [RefundController::class, 'store']);
Route::get('refunds/{refund}', [RefundController::class, 'show']);
Route::patch('refunds/{refund}/approve', [RefundController::class, 'approve']);
Route::patch('refunds/{refund}/process', [RefundController::class, 'process']);
Route::get('/languages', [TranslationController::class, 'languages']);
Route::get('/translations', [TranslationController::class, 'translate']);
Route::get('/translations/all', [TranslationController::class, 'all']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Save authenticated user's language preference
    Route::put('/user/language', [TranslationController::class, 'saveUserLanguage']);
});

Route::middleware(['auth:sanctum', 'permission:view_audit_logs'])->group(function () {
    Route::get(
        '/audit-logs',
        [AuditLogController::class, 'index']
    );

    // Must be before /{auditLog} to avoid being swallowed by the wildcard
    Route::get(
        '/audit-logs/actions',
        [AuditLogController::class, 'actions']
    );

    Route::get(
        '/audit-logs/user/{userId}',
        [AuditLogController::class, 'userLogs']
    );

    Route::get(
        '/audit-logs/{auditLog}',
        [AuditLogController::class, 'show']
    );

});
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewRatingController::class, 'store']);
        Route::get('/doctor/{doctorId}', [ReviewRatingController::class, 'doctorReviews']);
        Route::get('/patient/{patientId}', [ReviewRatingController::class, 'patientReviews']);
        Route::get('/doctor/{doctorId}/rating', [ReviewRatingController::class, 'doctorRating']);
        Route::put('/{reviewId}', [ReviewRatingController::class, 'update']);
        Route::delete('/{reviewId}', [ReviewRatingController::class, 'destroy']);
    });
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/medical-documents', [MedicalDocumentController::class, 'index'])
        ->middleware('permission:view_emr');
    Route::post('/medical-documents', [MedicalDocumentController::class, 'store'])
        ->middleware('permission:create_emr');
    Route::post('/medical-documents/{medicalDocument}', [MedicalDocumentController::class, 'update'])
        ->middleware('permission:update_emr');
    Route::put('/medical-documents/{medicalDocument}', [MedicalDocumentController::class, 'update'])
        ->middleware('permission:update_emr');
    Route::delete('/medical-documents/{medicalDocument}', [MedicalDocumentController::class, 'destroy'])
        ->middleware('permission:update_emr');
    Route::get('/patients/{patientId}/medical-documents', [MedicalDocumentController::class, 'patientDocuments'])
        ->middleware('permission:view_emr');
    Route::get('/encounters/{encounterId}/medical-documents', [MedicalDocumentController::class, 'encounterDocuments'])
        ->middleware('permission:view_emr');
    Route::get('/medical-documents/{medicalDocument}/download', [MedicalDocumentController::class, 'download'])
        ->middleware('permission:view_emr');
});
Route::middleware('auth:sanctum')->prefix('reports')->group(function () {
    Route::get('/patients', [ReportController::class, 'getPatientStatistics'])
        ->middleware('permission:view_reports');
    Route::get('/appointments', [ReportController::class, 'getAppointmentReport'])
        ->middleware('permission:view_reports');
    Route::get('/doctors/workload', [ReportController::class, 'getDoctorWorkload'])
        ->middleware('permission:view_reports');
    Route::get('/departments/performance', [ReportController::class, 'getDepartmentPerformance'])
        ->middleware('permission:view_reports');
    Route::get('/telehealth', [ReportController::class, 'getTelehealthStatistics'])
        ->middleware('permission:view_reports');
    Route::get('/trends', [ReportController::class, 'getHealthcareTrends'])
        ->middleware('permission:view_reports');
    Route::get('/hospitals/top', [ReportController::class, 'getTopHospitalsByVolume'])
        ->middleware('permission:view_reports');
    Route::get('/doctors/activity-heatmap', [ReportController::class, 'getDoctorActivityHeatmap'])
        ->middleware('permission:view_reports');
    Route::post('/custom/{reportId}', [ReportController::class, 'generateCustomReport'])
        ->middleware('permission:view_reports');
    Route::post('/', [ReportController::class, 'store'])
        ->middleware('permission:view_reports');
    Route::get('/doctor-ratings', [ReportController::class, 'getDoctorRatingStatistics'])
        ->middleware('permission:view_reports');
    Route::get('/export/excel/{type}', [ReportController::class, 'exportExcel'])
        ->middleware('permission:export_reports');
    Route::get('/export/csv/{type}', [ReportController::class, 'exportCsv'])
        ->middleware('permission:export_reports');
    Route::get('/export/pdf/{type}', [ReportController::class, 'exportPdf'])
        ->middleware('permission:export_reports');
});
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->middleware('permission:view_notifications');
    Route::post('/notifications', [NotificationController::class, 'store'])
        ->middleware('permission:send_notifications');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
        ->middleware('permission:view_notifications');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->middleware('permission:view_notifications');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])
        ->middleware('permission:view_notifications');
    Route::patch('/notifications/{notification}', [NotificationController::class, 'update'])
        ->middleware('permission:view_notifications');
    Route::patch('/notifications/{notification}/retry', [NotificationController::class, 'retry'])
        ->middleware('permission:send_notifications');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->middleware('permission:view_notifications');

    // User notification preferences
    Route::get('/notification-preferences', [NotificationController::class, 'getPreferences'])
        ->middleware('permission:view_notifications');
    Route::put('/notification-preferences', [NotificationController::class, 'updatePreferences'])
        ->middleware('permission:view_notifications');

});

// Notification Templates (platform_admin only)
Route::middleware('auth:sanctum')->prefix('notification-templates')->group(function () {
    Route::get('/', [NotificationTemplateController::class, 'index'])
        ->middleware('permission:view_notifications');
    Route::post('/', [NotificationTemplateController::class, 'store'])
        ->middleware('permission:send_notifications');
    Route::get('/{notificationTemplate}', [NotificationTemplateController::class, 'show'])
        ->middleware('permission:view_notifications');
    Route::put('/{notificationTemplate}', [NotificationTemplateController::class, 'update'])
        ->middleware('permission:send_notifications');
    Route::delete('/{notificationTemplate}', [NotificationTemplateController::class, 'destroy'])
        ->middleware('permission:send_notifications');
});

Route::middleware('auth:sanctum')->prefix('symptoms')->group(function () {
    Route::get('/', [SymptomController::class, 'index'])
        ->middleware('permission:use_symptom_checker');
    Route::get('/{id}', [SymptomController::class, 'show'])
        ->middleware('permission:use_symptom_checker');
    Route::post('/', [SymptomController::class, 'store'])
        ->middleware('permission:manage_symptoms');
    Route::put('/{id}', [SymptomController::class, 'update'])
        ->middleware('permission:manage_symptoms');
    Route::delete('/{id}', [SymptomController::class, 'destroy'])
        ->middleware('permission:manage_symptoms');
});
Route::middleware('auth:sanctum')->prefix('symptom-analytics')->group(function () {
    Route::post('/', [SymptomAnalyticsController::class, 'store'])
        ->middleware('permission:use_symptom_checker');
    Route::get('/', [SymptomAnalyticsController::class, 'index'])
        ->middleware('permission:use_symptom_checker');
    Route::get('/top-symptoms', [SymptomAnalyticsController::class, 'topSymptoms'])
        ->middleware('permission:use_symptom_checker');
});
Route::middleware('auth:sanctum')->prefix('symptom-mappings')->group(function () {
    Route::get('/recommendations-with-appointment/{symptomId}', [SymptomDepartmentMappingController::class, 'recommendationsWithAppointment']);
    Route::post('/', [SymptomDepartmentMappingController::class, 'store']); // Admin only
    Route::put('/{id}', [SymptomDepartmentMappingController::class, 'update']); // Admin only
    Route::delete('/{id}', [SymptomDepartmentMappingController::class, 'destroy']); // Admin only
    Route::get('/symptom/{symptomId}', [SymptomDepartmentMappingController::class, 'indexBySymptom']);
    Route::post('/{symptomId}/create-appointment', [SymptomDepartmentMappingController::class, 'createAppointment']);
});
// Public Google OAuth routes — must be defined BEFORE the auth:sanctum telehealth group
// so the /{id} wildcard doesn't intercept them first.
Route::get(
    '/telehealth-sessions/google/auth',
    [GoogleAuthController::class, 'redirectToGoogle']
);

Route::get(
    '/telehealth-sessions/oauth2callback',
    [GoogleAuthController::class, 'handleCallback']
);

Route::middleware('auth:sanctum')->prefix('telehealth-sessions/{sessionId}/attendance')->group(function () {
    Route::get('/', [TelehealthAttendanceController::class, 'index'])
        ->middleware('permission:join_telehealth_session');
    Route::post('/', [TelehealthAttendanceController::class, 'store'])
        ->middleware('permission:join_telehealth_session');
    Route::put('/{userId}', [TelehealthAttendanceController::class, 'update'])
        ->middleware('permission:join_telehealth_session');
});
Route::middleware('auth:sanctum')->prefix('telehealth-sessions')->group(function () {
    Route::post('/', [TelehealthSessionController::class, 'store'])
        ->middleware('permission:create_telehealth_session');
    Route::get('/my-sessions', [TelehealthSessionController::class, 'mySessions'])
        ->middleware('permission:join_telehealth_session');
    Route::get('/appointment/{appointmentId}', [TelehealthSessionController::class, 'byAppointment'])
        ->middleware('permission:join_telehealth_session');
    Route::get('/{id}', [TelehealthSessionController::class, 'show'])
        ->middleware('permission:join_telehealth_session');
    Route::put('/{id}', [TelehealthSessionController::class, 'update'])
        ->middleware('permission:create_telehealth_session');
    Route::post('/{id}/start', [TelehealthSessionController::class, 'start'])
        ->middleware('permission:create_telehealth_session');
    Route::post('/{id}/complete', [TelehealthSessionController::class, 'complete'])
        ->middleware('permission:create_telehealth_session');
    Route::post('/{id}/cancel', [TelehealthSessionController::class, 'cancel'])
        ->middleware('permission:create_telehealth_session');
    Route::post('/{id}/reschedule', [TelehealthSessionController::class, 'reschedule'])
        ->middleware('permission:create_telehealth_session');
    Route::post('/google-meet', [TelehealthSessionController::class, 'storeGoogleMeet'])
        ->middleware('permission:create_telehealth_session');
});
Route::middleware('auth:sanctum')
    ->prefix('vitals')
    ->group(function () {

        Route::post(
            '/',
            [VitalController::class, 'store']
        )->middleware('permission:create_emr');

        Route::get(
            '/{vital}',
            [VitalController::class, 'show']
        )->middleware('permission:view_emr');

        Route::put(
            '/{vital}',
            [VitalController::class, 'update']
        )->middleware('permission:update_emr');

        Route::delete(
            '/{vital}',
            [VitalController::class, 'destroy']
        )->middleware('permission:update_emr');

    });
Route::middleware('auth:sanctum')
    ->prefix('prescriptions')
    ->group(function () {

        Route::get(
            '/',
            [PrescriptionController::class, 'index']
        )->middleware('permission:view_prescription');

        Route::post(
            '/',
            [PrescriptionController::class, 'store']
        )->middleware('permission:create_prescription');

        Route::get(
            '/{prescription}',
            [PrescriptionController::class, 'show']
        )->middleware('permission:view_prescription');

        Route::put(
            '/{prescription}',
            [PrescriptionController::class, 'update']
        )->middleware('permission:create_prescription');

        Route::patch(
            '/{prescription}/complete',
            [PrescriptionController::class, 'complete']
        )->middleware('permission:create_prescription');

        Route::patch(
            '/{prescription}/cancel',
            [PrescriptionController::class, 'cancel']
        )->middleware('permission:create_prescription');

    });
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource(
        'medical-encounters',
        MedicalEncounterController::class
    )->only([
        'store',
        'show',
        'update',
    ]);

    // List encounters for the authenticated doctor or patient
    Route::get('/medical-encounters', [MedicalEncounterController::class, 'index'])
        ->middleware('permission:view_emr');

    Route::patch(
        '/medical-encounters/{medicalEncounter}/complete',
        [MedicalEncounterController::class, 'complete']
    )->middleware('permission:update_emr');

    // Doctor: get full encounter history for a specific patient
    Route::get('/medical-encounters/patient/{patientId}', [MedicalEncounterController::class, 'patientHistory'])
        ->middleware('permission:view_emr');

    // Doctor: update patient persistent medical profile (blood_type, allergies, medical_history)
    Route::patch('/medical-encounters/{encounterId}/patient-medical', [MedicalEncounterController::class, 'updatePatientMedical'])
        ->middleware('permission:update_emr');

});
Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/consultations/{queue}',
        [ConsultationController::class, 'show']
    );

});

Route::middleware('auth:sanctum')->group(function () {

    
    Route::get('/doctor/me', function () {
        $user = auth()->user();
        $provider = \App\Models\HealthcareProvider::with(['user','department','hospital'])
            ->find($user->id);
        if (!$provider) {
            return response()->json(['message' => 'Not a doctor'], 403);
        }
        return new \App\Http\Resources\HealthcareProviderResource($provider);
    });

    
    Route::post('/doctor/me', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $provider = \App\Models\HealthcareProvider::findOrFail($user->id);
        $data = $request->validate([
            'bio'                    => 'nullable|string',
            'consultation_fee'       => 'nullable|numeric',
            'is_telehealth_available'=> 'boolean',
            'profile_picture'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($request->hasFile('profile_picture')) {
            if ($provider->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($provider->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('doctor-profiles','public');
        }
        // remove _method from the data if present
        unset($data['_method']);
        $provider->update($data);
        return new \App\Http\Resources\HealthcareProviderResource($provider->fresh(['user','department','hospital']));
    });

    Route::put('/doctor/me', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $provider = \App\Models\HealthcareProvider::findOrFail($user->id);
        $data = $request->validate([
            'bio'                    => 'nullable|string',
            'consultation_fee'       => 'nullable|numeric',
            'is_telehealth_available'=> 'boolean',
            'profile_picture'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($request->hasFile('profile_picture')) {
            if ($provider->profile_picture) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($provider->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('doctor-profiles','public');
        }
        $provider->update($data);
        return new \App\Http\Resources\HealthcareProviderResource($provider->fresh(['user','department','hospital']));
    });

   
    Route::get('/doctor/my-schedules', function () {
        $schedules = \App\Models\DoctorSchedule::where('doctor_id', auth()->id())
            ->orderBy('day_of_week')
            ->get();
        return response()->json(['data' => $schedules]);
    });

});
Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/doctor-schedules',
        [DoctorScheduleController::class, 'index']
    )->middleware('permission:manage_schedule');

    Route::post(
        '/doctor-schedules',
        [DoctorScheduleController::class, 'store']
    )->middleware('permission:manage_schedule');

    Route::put(
        '/doctor-schedules/{doctorSchedule}',
        [DoctorScheduleController::class, 'update']
    )->middleware('permission:manage_schedule');

    Route::delete(
        '/doctor-schedules/{doctorSchedule}',
        [DoctorScheduleController::class, 'destroy']
    )->middleware('permission:manage_schedule');

   Route::get(
    '/doctor-schedules/{doctorSchedule}',
    [DoctorScheduleController::class, 'show']
)->middleware('permission:manage_schedule');
});
Route::middleware('auth:sanctum')
->group(function(){

Route::get(
'/healthcare-providers',
[
HealthcareProviderController::class,
'index'
]
)->middleware('permission:view_doctors');

Route::post(
'/healthcare-providers',
[
HealthcareProviderController::class,
'store'
]
)->middleware('permission:create_doctors');

Route::put(
'/healthcare-providers/{provider}',
[
HealthcareProviderController::class,
'update'
]
)->middleware('permission:update_doctors');

Route::get('/healthcare-providers/{provider}',[HealthcareProviderController::class,'show'])
    ->middleware('permission:view_doctors');

Route::delete(
'/healthcare-providers/{provider}',
[
HealthcareProviderController::class,
'destroy'
]
)->middleware('permission:delete_doctors');

});

// ── Roles & Permissions ───────────────────────────────────────────────────────
Route::middleware(['auth:sanctum'])->group(function () {

    // Permissions — read only (both platform_admin and hospital_admin)
    Route::get('/permissions', [\App\Http\Controllers\Api\PermissionController::class, 'index'])
        ->middleware('permission:view_permissions');

    // Roles CRUD
    Route::get('/roles', [\App\Http\Controllers\Api\RoleController::class, 'index'])
        ->middleware('permission:view_roles');

    Route::post('/roles', [\App\Http\Controllers\Api\RoleController::class, 'store'])
        ->middleware('permission:create_roles');

    Route::get('/roles/{role}', [\App\Http\Controllers\Api\RoleController::class, 'show'])
        ->middleware('permission:view_roles');

    Route::put('/roles/{role}', [\App\Http\Controllers\Api\RoleController::class, 'update'])
        ->middleware('permission:update_roles');

    Route::delete('/roles/{role}', [\App\Http\Controllers\Api\RoleController::class, 'destroy'])
        ->middleware('permission:delete_roles');

    // Assign / sync permissions on a role
    Route::put('/roles/{role}/permissions', [\App\Http\Controllers\Api\RoleController::class, 'syncPermissions'])
        ->middleware('permission:assign_permissions');

    // List users attached to a role
    Route::get('/roles/{role}/users', [\App\Http\Controllers\Api\RoleController::class, 'users'])
        ->middleware('permission:view_roles');
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [
    AuthController::class,
    'login',
]
);
Route::post(
    '/forgot-password',
    [
        PasswordResetController::class,
        'forgotPassword',
    ]
);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post(
    '/reset-password',
    [
        PasswordResetController::class,
        'resetPassword',
    ]
);

// Staff invitation – public routes (no auth required)
Route::get('/accept-invitation/check', [\App\Http\Controllers\Api\InvitationController::class, 'check']);
Route::post('/accept-invitation',      [\App\Http\Controllers\Api\InvitationController::class, 'accept']);

Route::middleware(['auth:sanctum',  'permission:view_users',

])
    ->group(function () {

        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);

    });

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/hospitals',
        [HospitalController::class, 'index']
    )->middleware('permission:view_hospitals');

    Route::get(
        '/hospitals/{hospital}',
        [HospitalController::class, 'show']
    )->middleware('permission:view_hospitals');

    Route::post(
        '/hospitals',
        [HospitalController::class, 'store']
    )->middleware('permission:create_hospitals');

    Route::put(
        '/hospitals/{hospital}',
        [HospitalController::class, 'update']
    )->middleware('permission:update_hospitals');

    Route::delete(
        '/hospitals/{hospital}',
        [HospitalController::class, 'destroy']
    )->middleware('permission:delete_hospitals');

});
Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/departments',
        [DepartmentController::class, 'index']
    )->middleware('permission:view_departments');

    Route::get(
        '/departments/{department}',
        [DepartmentController::class, 'show']
    )->middleware('permission:view_departments');

    Route::post(
        '/departments',
        [DepartmentController::class, 'store']
    )->middleware('permission:create_departments');

    Route::put(
        '/departments/{department}',
        [DepartmentController::class, 'update']
    )->middleware('permission:update_departments');

    Route::delete(
        '/departments/{department}',
        [DepartmentController::class, 'destroy']
    )->middleware('permission:delete_departments');

});
Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/facilities',
        [FacilityController::class, 'index']
    )->middleware('permission:view_facilities');

    Route::get(
        '/facilities/{facility}',
        [FacilityController::class, 'show']
    )->middleware('permission:view_facilities');

    Route::post(
        '/facilities',
        [FacilityController::class, 'store']
    )->middleware('permission:create_facilities');

    Route::put(
        '/facilities/{facility}',
        [FacilityController::class, 'update']
    )->middleware('permission:update_facilities');

    Route::delete(
        '/facilities/{facility}',
        [FacilityController::class, 'destroy']
    )->middleware('permission:delete_facilities');

});
Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/operating-hours',
        [HospitalOperatingHourController::class, 'index']
    )->middleware('permission:view_operating_hours');

    Route::get(
        '/operating-hours/{operatingHour}',
        [HospitalOperatingHourController::class, 'show']
    )->middleware('permission:view_operating_hours');

    Route::post(
        '/operating-hours',
        [HospitalOperatingHourController::class, 'store']
    )->middleware('permission:create_operating_hours');

    Route::put(
        '/operating-hours/{operatingHour}',
        [HospitalOperatingHourController::class, 'update']
    )->middleware('permission:update_operating_hours');

    Route::delete(
        '/operating-hours/{operatingHour}',
        [HospitalOperatingHourController::class, 'destroy']
    )->middleware('permission:delete_operating_hours');

});



Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/hospital-staff',
        [HospitalStaffController::class, 'index']
    )->middleware('permission:view_users');

    Route::post(
        '/hospital-staff',
        [HospitalStaffController::class, 'store']
    )->middleware('permission:create_users');

    Route::put(
        '/hospital-staff/{user}',
        [HospitalStaffController::class, 'update']
    )->middleware('permission:update_users');

    Route::delete(
        '/hospital-staff/{user}',
        [HospitalStaffController::class, 'destroy']
    )->middleware('permission:delete_users');

});


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/appointments', [AppointmentController::class, 'index'])
        ->middleware('permission:view_appointments');

    Route::post('/appointments', [AppointmentController::class, 'store'])
        ->middleware('permission:book_appointment');

    // Hospital-admin: list available doctors+slots for a given hospital/department/date
    // IMPORTANT: must be defined before {appointment} wildcard routes
    Route::get(
        '/appointments/available-doctor-slots',
        [AppointmentController::class, 'availableDoctorSlots']
    )->middleware('permission:view_appointments');

    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])
        ->middleware('permission:view_appointments');

    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])
        ->middleware('permission:reschedule_appointment');

    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])
        ->middleware('permission:cancel_appointment');

    Route::patch('/appointments/{appointment}/hide', [AppointmentController::class, 'hideFromPatient'])
        ->middleware('permission:view_appointments');

    Route::put(
        '/appointments/{appointment}/reschedule',
        [AppointmentController::class, 'reschedule']
    )->middleware('permission:reschedule_appointment');

    // Hospital-admin: reassign a leave-affected appointment to a different doctor
    Route::put(
        '/appointments/{appointment}/admin-reschedule',
        [AppointmentController::class, 'adminReschedule']
    )->middleware('permission:reschedule_appointment');

    // ── Appointment referrals ─────────────────────────────────────────────
    Route::post('/appointments/{appointment}/refer',
        [\App\Http\Controllers\Api\AppointmentReferralController::class, 'refer'])
        ->middleware('permission:view_appointments');

    Route::get('/appointments/{appointment}/referrals',
        [\App\Http\Controllers\Api\AppointmentReferralController::class, 'forAppointment'])
        ->middleware('permission:view_appointments');

    Route::get('/appointment-referrals/incoming',
        [\App\Http\Controllers\Api\AppointmentReferralController::class, 'incoming'])
        ->middleware('permission:view_appointments');

    Route::patch('/appointment-referrals/{referral}/respond',
        [\App\Http\Controllers\Api\AppointmentReferralController::class, 'respond'])
        ->middleware('permission:view_appointments');

});
Route::middleware('auth:sanctum')->group(function () {
Route::get(
    '/doctor-leaves',
    [DoctorLeaveController::class, 'index']
)->middleware('permission:manage_leave');

Route::get(
    '/doctor-leaves/{doctorLeave}',
    [DoctorLeaveController::class, 'show']
)->middleware('permission:manage_leave');

Route::post(
    '/doctor-leaves',
    [DoctorLeaveController::class, 'store']
)->middleware('permission:manage_leave');

Route::put(
    '/doctor-leaves/{doctorLeave}',
    [DoctorLeaveController::class, 'update']
)->middleware('permission:manage_leave');

Route::delete(
    '/doctor-leaves/{doctorLeave}',
    [DoctorLeaveController::class, 'destroy']
)->middleware('permission:manage_leave');

Route::patch(
    '/doctor-leaves/{doctorLeave}/approve',
    [DoctorLeaveController::class, 'approve']
)->middleware('permission:manage_leave');
});
Route::middleware('auth:sanctum')->prefix('queue')->group(function () {

    Route::post('/generate', [QueueController::class, 'generate'])
        ->middleware('permission:manage_queue');
    Route::post('/init', [QueueController::class, 'init'])
        ->middleware('permission:manage_queue');
    Route::post('/call-next', [QueueController::class, 'callNext'])
        ->middleware('permission:call_next_patient');
    Route::post('/skip', [QueueController::class, 'skip'])
        ->middleware('permission:manage_queue');
    Route::post('/complete', [QueueController::class, 'complete'])
        ->middleware('permission:manage_queue');
    Route::post('/recall', [QueueController::class, 'recall'])
        ->middleware('permission:manage_queue');
    Route::get('/doctor/{doctorId}', [QueueController::class, 'doctorQueue'])
        ->middleware('permission:view_queue');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post(
        '/slots/generate',
        [AppointmentSlotController::class, 'generate']
    );

    Route::post(
        '/slots/block',
        [AppointmentSlotController::class, 'block']
    );

    Route::post(
        '/slots/{slot}/book',
        [AppointmentSlotController::class, 'book']
    );

    Route::post(
        '/slots/{slot}/release',
        [AppointmentSlotController::class, 'release']
    );

    Route::post(
        '/slots/{slot}/complete',
        [AppointmentSlotController::class, 'complete']
    );

    Route::get(
        '/slots',
        function () {
            return \App\Models\AppointmentSlot::with('doctor.user')->get();
        }
    );

    Route::get(
        '/slots/available',
        function (\Illuminate\Http\Request $request) {
            $request->validate([
                'doctor_id' => ['required', 'uuid'],
                'date'      => ['required', 'date'],
            ]);

            $doctorId = $request->doctor_id;
            $date     = $request->date;

            // Auto-generate slots if none exist yet (idempotent)
            $doctor = \App\Models\HealthcareProvider::find($doctorId);
            if ($doctor) {
                try {
                    app(\App\Services\AppointmentSlotService::class)
                        ->generateSlots($doctor, \Carbon\Carbon::parse($date));
                } catch (\Throwable $e) {
                    // silent — slots may already exist
                }
            }

            $slots = \App\Models\AppointmentSlot::where('doctor_id', $doctorId)
                ->where('status', 'available')
                ->whereDate('start_time', $date)
                ->orderBy('start_time')
                ->get();

            return response()->json(['data' => $slots]);
        }
    );

});
Route::middleware('auth:sanctum')->group(function () {

    Route::put(
        '/patient/profile',
        [PatientProfileController::class, 'completeProfile']
    );

    // Get current patient's own profile
    Route::get(
        '/patient/profile',
        function () {
            $user    = auth()->user();
            $patient = $user->patient;
            if (!$patient) {
                return response()->json(['message' => 'Patient profile not found'], 404);
            }
            return response()->json([
                'data' => $patient->load(['user', 'emergencyContacts'])
            ]);
        }
    );

    Route::post(
        '/patient/emergency-contacts',
        [PatientProfileController::class, 'storeEmergencyContact']
    );

    Route::put(
        '/patient/emergency-contacts/{contact}',
        [PatientProfileController::class, 'updateEmergencyContact']
    );

    Route::delete(
        '/patient/emergency-contacts/{contact}',
        [PatientProfileController::class, 'deleteEmergencyContact']
    );

});

// ── Receptionist: Patient registration & search ───────────────────────────────
Route::middleware('auth:sanctum')->prefix('receptionist')->group(function () {
    Route::get('/patients/search', [ReceptionistPatientController::class, 'search']);
    Route::get('/patients',        [ReceptionistPatientController::class, 'index']);
    Route::post('/patients',       [ReceptionistPatientController::class, 'store']);
});
