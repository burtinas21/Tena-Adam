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
Route::middleware(['auth:sanctum'])->group(function () {

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
    Route::get('/medical-documents', [MedicalDocumentController::class, 'index']);
    Route::post('/medical-documents', [MedicalDocumentController::class, 'store']);
    Route::post('/medical-documents/{medicalDocument}', [MedicalDocumentController::class, 'update']);
    Route::put('/medical-documents/{medicalDocument}', [MedicalDocumentController::class, 'update']);
    Route::delete('/medical-documents/{medicalDocument}', [MedicalDocumentController::class, 'destroy']);
    Route::get('/patients/{patientId}/medical-documents', [MedicalDocumentController::class, 'patientDocuments']);
    Route::get('/encounters/{encounterId}/medical-documents', [MedicalDocumentController::class, 'encounterDocuments']);
    Route::get('/medical-documents/{medicalDocument}/download', [MedicalDocumentController::class, 'download']);
});
Route::middleware('auth:sanctum')->prefix('reports')->group(function () {
    Route::get('/patients', [ReportController::class, 'getPatientStatistics']);
    Route::get('/appointments', [ReportController::class, 'getAppointmentReport']);
    Route::get('/doctors/workload', [ReportController::class, 'getDoctorWorkload']);
    Route::get('/departments/performance', [ReportController::class, 'getDepartmentPerformance']);
    Route::get('/telehealth', [ReportController::class, 'getTelehealthStatistics']);
    Route::get('/trends', [ReportController::class, 'getHealthcareTrends']);
    Route::get('/hospitals/top', [ReportController::class, 'getTopHospitalsByVolume']);
    Route::get('/doctors/activity-heatmap', [ReportController::class, 'getDoctorActivityHeatmap']);
    Route::post('/custom/{reportId}', [ReportController::class, 'generateCustomReport']);
    Route::post('/', [ReportController::class, 'store']);
    Route::get('/doctor-ratings', [ReportController::class, 'getDoctorRatingStatistics']);
 Route::get('/export/excel/{type}', [ReportController::class, 'exportExcel']);
    Route::get('/export/csv/{type}', [ReportController::class, 'exportCsv']);
    Route::get('/export/pdf/{type}', [ReportController::class, 'exportPdf']);
});
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::get('/notifications/{notification}', [NotificationController::class, 'show']);
    Route::patch('/notifications/{notification}', [NotificationController::class, 'update']);
    Route::patch('/notifications/{notification}/retry', [NotificationController::class, 'retry']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);

    // User notification preferences
    Route::get('/notification-preferences', [NotificationController::class, 'getPreferences']);
    Route::put('/notification-preferences', [NotificationController::class, 'updatePreferences']);

});

// Notification Templates (platform_admin only)
Route::middleware('auth:sanctum')->prefix('notification-templates')->group(function () {
    Route::get('/', [NotificationTemplateController::class, 'index']);
    Route::post('/', [NotificationTemplateController::class, 'store']);
    Route::get('/{notificationTemplate}', [NotificationTemplateController::class, 'show']);
    Route::put('/{notificationTemplate}', [NotificationTemplateController::class, 'update']);
    Route::delete('/{notificationTemplate}', [NotificationTemplateController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('symptoms')->group(function () {
    Route::get('/', [SymptomController::class, 'index']);           // everyone
    Route::get('/{id}', [SymptomController::class, 'show']);        // everyone
    Route::post('/', [SymptomController::class, 'store']);          // admin only
    Route::put('/{id}', [SymptomController::class, 'update']);      // admin only
    Route::delete('/{id}', [SymptomController::class, 'destroy']);  // admin only
});
Route::middleware('auth:sanctum')->prefix('symptom-analytics')->group(function () {
    Route::post('/', [SymptomAnalyticsController::class, 'store']); // Patients log analytics
    Route::get('/', [SymptomAnalyticsController::class, 'index']); // Admins/Doctors view all
    Route::get('/top-symptoms', [SymptomAnalyticsController::class, 'topSymptoms']); // Admins/Doctors view top 10
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
    Route::get('/', [TelehealthAttendanceController::class, 'index']); // list attendance
    Route::post('/', [TelehealthAttendanceController::class, 'store']); // join session
    Route::put('/{userId}', [TelehealthAttendanceController::class, 'update']); // leave session
});
Route::middleware('auth:sanctum')->prefix('telehealth-sessions')->group(function () {
    Route::post('/', [TelehealthSessionController::class, 'store']);
    Route::get('/my-sessions', [TelehealthSessionController::class, 'mySessions']);
    Route::get('/appointment/{appointmentId}', [TelehealthSessionController::class, 'byAppointment']);
    Route::get('/{id}', [TelehealthSessionController::class, 'show']);
    Route::put('/{id}', [TelehealthSessionController::class, 'update']);
    Route::post('/{id}/start', [TelehealthSessionController::class, 'start']);
    Route::post('/{id}/complete', [TelehealthSessionController::class, 'complete']);
    Route::post('/{id}/cancel', [TelehealthSessionController::class, 'cancel']);
    Route::post('/google-meet', [TelehealthSessionController::class, 'storeGoogleMeet']);
});
Route::middleware('auth:sanctum')
    ->prefix('vitals')
    ->group(function () {

        Route::post(
            '/',
            [VitalController::class, 'store']
        );

        Route::get(
            '/{vital}',
            [VitalController::class, 'show']
        );

        Route::put(
            '/{vital}',
            [VitalController::class, 'update']
        );

        Route::delete(
            '/{vital}',
            [VitalController::class, 'destroy']
        );

    });
Route::middleware('auth:sanctum')
    ->prefix('prescriptions')
    ->group(function () {

        Route::get(
            '/',
            [PrescriptionController::class, 'index']
        );

        Route::post(
            '/',
            [PrescriptionController::class, 'store']
        );

        Route::get(
            '/{prescription}',
            [PrescriptionController::class, 'show']
        );

        Route::put(
            '/{prescription}',
            [PrescriptionController::class, 'update']
        );

        Route::patch(
            '/{prescription}/complete',
            [PrescriptionController::class, 'complete']
        );

        Route::patch(
            '/{prescription}/cancel',
            [PrescriptionController::class, 'cancel']
        );

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
    Route::get('/medical-encounters', [MedicalEncounterController::class, 'index']);

    Route::patch(
        '/medical-encounters/{medicalEncounter}/complete',
        [MedicalEncounterController::class, 'complete']
    );

    // Doctor: get full encounter history for a specific patient
    Route::get('/medical-encounters/patient/{patientId}', [MedicalEncounterController::class, 'patientHistory']);

    // Doctor: update patient persistent medical profile (blood_type, allergies, medical_history)
    Route::patch('/medical-encounters/{encounterId}/patient-medical', [MedicalEncounterController::class, 'updatePatientMedical']);

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
    );

    Route::post(
        '/doctor-schedules',
        [DoctorScheduleController::class, 'store']
    );

    Route::put(
        '/doctor-schedules/{doctorSchedule}',
        [DoctorScheduleController::class, 'update']
    );

    Route::delete(
        '/doctor-schedules/{doctorSchedule}',
        [DoctorScheduleController::class, 'destroy']
    );

   Route::get(
    '/doctor-schedules/{doctorSchedule}',
    [DoctorScheduleController::class, 'show']
);
});
Route::middleware('auth:sanctum')
->group(function(){


Route::get(
'/healthcare-providers',
[
HealthcareProviderController::class,
'index'
]
);



Route::post(
'/healthcare-providers',
[
HealthcareProviderController::class,
'store'
]
);



Route::put(
'/healthcare-providers/{provider}',
[
HealthcareProviderController::class,
'update'
]
);
Route::get('/healthcare-providers/{provider}',[HealthcareProviderController::class,'show']);
Route::delete(
'/healthcare-providers/{provider}',
[
HealthcareProviderController::class,
'destroy'
]
);



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

Route::middleware(['auth:sanctum',  'permission:view_users',

])
    ->group(function () {

        Route::get('/users', function () {

            return response()->json([

                'message' => 'You have manage_users permission',

            ]);

        });

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

    Route::get('/appointments', [AppointmentController::class, 'index']);

    Route::post('/appointments', [AppointmentController::class, 'store']);

    // Hospital-admin: list available doctors+slots for a given hospital/department/date
    // IMPORTANT: must be defined before {appointment} wildcard routes
    Route::get(
        '/appointments/available-doctor-slots',
        [AppointmentController::class, 'availableDoctorSlots']
    );

    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);

    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);

    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);
    Route::put(
    '/appointments/{appointment}/reschedule',
    [AppointmentController::class, 'reschedule']
);

    // Hospital-admin: reassign a leave-affected appointment to a different doctor
    Route::put(
        '/appointments/{appointment}/admin-reschedule',
        [AppointmentController::class, 'adminReschedule']
    );

});
Route::middleware('auth:sanctum')->group(function () {
Route::get(
    '/doctor-leaves',
    [DoctorLeaveController::class, 'index']
);

Route::get(
    '/doctor-leaves/{doctorLeave}',
    [DoctorLeaveController::class, 'show']
);

Route::post(
    '/doctor-leaves',
    [DoctorLeaveController::class, 'store']
);

Route::put(
    '/doctor-leaves/{doctorLeave}',
    [DoctorLeaveController::class, 'update']
);

Route::delete(
    '/doctor-leaves/{doctorLeave}',
    [DoctorLeaveController::class, 'destroy']
);

Route::patch(
    '/doctor-leaves/{doctorLeave}/approve',
    [DoctorLeaveController::class, 'approve']
);
});
Route::middleware('auth:sanctum')->prefix('queue')->group(function () {

    Route::post('/generate', [QueueController::class, 'generate']);
    Route::post('/init', [QueueController::class, 'init']);
    Route::post('/call-next', [QueueController::class, 'callNext']);
    Route::post('/skip', [QueueController::class, 'skip']);
    Route::post('/complete', [QueueController::class, 'complete']);
    Route::post('/recall', [QueueController::class, 'recall']);
    Route::get('/doctor/{doctorId}', [QueueController::class, 'doctorQueue']);
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
