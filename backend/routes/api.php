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
Route::middleware('auth:sanctum')  ->prefix('vitals')  ->group(function () {
        Route::post('/', [VitalController::class, 'store']);
        Route::put('/{id}', [VitalController::class, 'update']);
        Route::get('/{id}', [VitalController::class, 'show']);
    });
Route::middleware('auth:sanctum')  ->prefix('prescriptions')->group(function () {
        Route::post('/', [PrescriptionController::class, 'store']);
        Route::put('/{id}', [PrescriptionController::class, 'update']);
        Route::get('/{id}', [PrescriptionController::class, 'show']);
        Route::delete('/{id}', [PrescriptionController::class, 'cancel']);
    });
Route::middleware('auth:sanctum')
->prefix('medical-encounters')
->group(function(){

    Route::post(
        '/',
        [MedicalEncounterController::class,'store']
    );
    Route::get(
        '/{id}',
        [MedicalEncounterController::class,'show']
    );

    Route::put(
        '/{id}',
        [MedicalEncounterController::class,'update']
    );


    Route::post(
        '/{id}/complete',
        [MedicalEncounterController::class,'complete']
    );


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
