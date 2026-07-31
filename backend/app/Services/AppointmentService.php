<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\DoctorSchedule;
use App\Models\HealthcareProvider;
use Carbon\Carbon;
use App\Services\NotificationService;
use App\Services\TelehealthSessionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\PaymentService;

class AppointmentService

{
    private function normalizeTime(string $time): string
    {
        return substr($time, 0, 5);
    }
 protected NotificationService $notificationService;
 protected TelehealthSessionService $telehealthService;
public function __construct(
    NotificationService $notificationService,
    TelehealthSessionService $telehealthService,
     private PaymentService $paymentService
) {
    $this->notificationService = $notificationService;
    $this->telehealthService   = $telehealthService;
}

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Save optional uploaded files and attach them to the appointment.
     * Files come as an array of UploadedFile instances.
     */
    private function attachUploadedFiles(Appointment $appointment, array $files): void
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];

        foreach ($files as $file) {
            if (! $file || ! in_array($file->getMimeType(), $allowedMimes)) {
                continue;
            }

            $filePath = $file->store('appointment-documents', 'public');

            \App\Models\MedicalDocument::create([
                'patient_id'     => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'encounter_id'   => null,
                'file_name'      => $file->getClientOriginalName(),
                'file_url'       => $filePath,
                'file_type'      => $file->getMimeType(),
                'file_size'      => $file->getSize(),
                'document_type'  => 'appointment_upload',
                'uploaded_by'    => auth()->id(),
                'description'    => 'Uploaded at appointment booking',
            ]);
        }
    }

    public function all()
    {
        $user  = auth()->user();
        $query = Appointment::with([
            'patient',
            'doctor.user',
            'hospital',
            'department',
            'approvedBy',
            'slot',
        ])->latest();

        if ($user->hasRole('platform_admin')) {
            // no filter — sees everything
        } elseif ($user->hasRole('hospital_admin')) {
            $hospitalIds = $user->hospitals()->pluck('hospitals.id');
            $query->whereIn('hospital_id', $hospitalIds);
        } elseif ($user->hasRole('receptionist')) {
            // scoped to their own hospital only
            $hospitalId = $user->hospitalStaff()->value('hospital_id');
            if ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            } else {
                return collect();
            }
        } elseif ($user->hasRole('doctor')) {
            $query->where('doctor_id', $user->id);
        } elseif ($user->hasRole('patient')) {
            $query->where('patient_id', $user->id)
                  ->where('patient_hidden', false);
        } else {
            return collect();
        }

        return $query->get();
    }

    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $authUser = auth()->user();

            // If a receptionist is booking on behalf of a patient, use the provided patient_id.
            // Otherwise (patient booking themselves) use the authenticated user.
            if ($authUser->hasRole('receptionist') && !empty($data['patient_id'])) {
                $patient        = \App\Models\User::findOrFail($data['patient_id']);
                $patientProfile = \App\Models\Patient::findOrFail($data['patient_id']);
            } else {
                $patient        = $authUser;
                $patientProfile = $authUser->patient;
            }

            if (! $patientProfile) {
                throw ValidationException::withMessages([
                    'patient' => ['Patient profile not found.']
                ]);
            }

            if ($patientProfile->patient_status !== 'active') {
                throw ValidationException::withMessages([
                    'patient' => ['Patient profile is not active.']
                ]);
            }
            $doctor       = HealthcareProvider::findOrFail($data['doctor_id']);
            $hospitalId   = $doctor->hospital_id;
            $departmentId = $doctor->department_id;

            // 2. Find doctor schedule for the requested day
            $day = Carbon::parse($data['appointment_date'])->dayOfWeek;

            $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
                ->where('day_of_week', $day)
                ->first();

            if (! $schedule) {
                throw ValidationException::withMessages([
                    'appointment_date' => ['Doctor does not work on this day.'],
                ]);
            }

            // 3. Schedule active?
            if (! $schedule->is_available) {
                throw ValidationException::withMessages([
                    'appointment_date' => ['Doctor is unavailable on this day.'],
                ]);
            }

            // 4. Inside working hours?
            $time = $this->normalizeTime($data['appointment_time']);

            if ($time < $this->normalizeTime($schedule->start_time) ||
                $time >= $this->normalizeTime($schedule->end_time)) {
                throw ValidationException::withMessages([
                    'appointment_time' => ['Appointment is outside working hours.'],
                ]);
            }

            // 5. Lunch break?
            if ($schedule->lunch_start && $schedule->lunch_end) {
                $ls = $this->normalizeTime($schedule->lunch_start);
                $le = $this->normalizeTime($schedule->lunch_end);

                if ($time >= $ls && $time < $le) {
                    throw ValidationException::withMessages([
                        'appointment_time' => ['Doctor is on lunch break at this time.'],
                    ]);
                }
            }

            $start    = Carbon::createFromFormat('H:i', $this->normalizeTime($schedule->start_time));
            $selected = Carbon::createFromFormat('H:i', $time);
            $minutes  = $start->diffInMinutes($selected);

            if ($minutes % $schedule->slot_duration_min !== 0) {
                throw ValidationException::withMessages([
                    'appointment_time' => [
                        'Invalid slot. Slots are every ' . $schedule->slot_duration_min . ' minutes.',
                    ],
                ]);
            }

            $scheduledDatetime = Carbon::parse($data['appointment_date'] . ' ' . $time);

            // Use a pessimistic lock on the slot row to prevent race conditions.
            // If the slot is already booked we throw a friendly validation error
            // before the DB unique constraint can fire.
            $slotEnd = (clone $scheduledDatetime)->addMinutes($schedule->slot_duration_min);

            $slot = AppointmentSlot::firstOrCreate(
                [
                    'doctor_id'  => $doctor->id,
                    'start_time' => $scheduledDatetime,
                ],
                [
                    'end_time' => $slotEnd,
                    'status'   => 'available',
                ]
            );

            // Lock the slot row for the duration of this transaction
            $slot = AppointmentSlot::lockForUpdate()->find($slot->id);

            if ($slot->status === 'booked') {
                throw ValidationException::withMessages([
                    'appointment_time' => ['This appointment slot is already booked. Please choose another time.'],
                ]);
            }

            // Also check for an existing active appointment at this time
            // (handles cases where slot table and appointments table diverge)
            $exists = Appointment::where('doctor_id', $doctor->id)
                ->where('scheduled_time', $scheduledDatetime)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->exists();

            if ($exists) {
                // Mark the slot as booked so UI won't offer it again
                $slot->update(['status' => 'booked']);
                throw ValidationException::withMessages([
                    'appointment_time' => ['This appointment slot is already booked. Please choose another time.'],
                ]);
            }

            $slot->update(['status' => 'booked']);

   
            $appointment = Appointment::create([
                'patient_id'    => $patient->id,
                'doctor_id'     => $doctor->id,                'hospital_id'   => $hospitalId,
                'department_id' => $departmentId,
                'slot_id'       => $slot->id,
                'scheduled_time'=> $scheduledDatetime,
                'duration_min'  => $schedule->slot_duration_min,
                // 'status'        => 'pending',
                'visit_type'      => $data['visit_type'] ?? 'normal',
                'status'          => 'pending_payment',
                'reason'        => $data['reason'],
                'notes'         => $data['notes'] ?? null,
                'is_telehealth' => $data['is_telehealth'] ?? false,
            ]);
            $payment = $this->paymentService->create([

            'appointment_id'    => $appointment->id,

            'patient_id'        => $appointment->patient_id,

            'hospital_id'       => $appointment->hospital_id,

            'payment_method_id' => $data['payment_method_id'] ?? null,

            'amount'            => $data['amount'] ?? ($appointment->doctor->consultation_fee ?? 0),

            'currency'          => 'ETB',

            'email'             => $patient->email ?? ($authUser->email ?? ''),

            'first_name'        => $patient->first_name ?? ($authUser->first_name ?? ''),

            'last_name'         => $patient->last_name ?? ($authUser->last_name ?? ''),

        ]);
            $this->notificationService
    ->sendAppointmentNotification(

        $appointment
            ->patient
            ->user,

        'Appointment Submitted',

        'Your appointment request has been submitted successfully.',

        false
    );

            // ── Optional file attachments ─────────────────────────────────
            if (! empty($data['uploaded_files'])) {
                $this->attachUploadedFiles($appointment, (array) $data['uploaded_files']);
            }

            // Notify doctor of the new appointment request
            $this->notificationService->sendAppointmentNotification(
                $appointment->doctor->user,
                'New Appointment Request',
                'A new appointment has been requested and is pending your confirmation.',
                false
            );

            // Notify receptionist and hospital admin about the new appointment
            $patientName = trim(($appointment->patient->user->first_name ?? '') . ' ' . ($appointment->patient->user->last_name ?? ''));
            $doctorName  = trim(($appointment->doctor->user->first_name ?? '') . ' ' . ($appointment->doctor->user->last_name ?? ''));
            $this->notificationService->sendStaffAppointmentNotification(
                $appointment,
                'New Appointment Booked',
                "A new appointment has been booked by {$patientName} with Dr. {$doctorName}."
            );

            $appointment->load([
    'patient',
    'doctor.user',
    'hospital',
    'department',
    'approvedBy',
    'slot',
    'documents',
]);


            return [

                'appointment' => $appointment,

                'payment' => $payment['payment'],

                'checkout_url' => $payment['checkout_url'],

            ];
        });
    }


    public function update(Appointment $appointment, array $data): Appointment
    {
        return DB::transaction(function () use ($appointment, $data) {

            $user = auth()->user();


            if (isset($data['status']) && $data['status'] === 'cancelled') {
                $data['cancelled_at'] = now();
                if ($appointment->slot_id) {
                    AppointmentSlot::where('id', $appointment->slot_id)
                        ->update(['status' => 'available']);
                }
            }

            if (isset($data['status']) && $data['status'] === 'completed') {
                $data['approved_by'] = $user->id;
                $data['approved_at'] = now();
                if ($appointment->slot_id) {
                    AppointmentSlot::where('id', $appointment->slot_id)
                        ->update(['status' => 'completed']);
                }
            }

            if (isset($data['status']) && $data['status'] === 'confirmed') {
                $data['approved_by'] = $user->id;
                $data['approved_at'] = now();
            }

            if (isset($data['appointment_date'], $data['appointment_time'])) {
                $time              = $this->normalizeTime($data['appointment_time']);
                $scheduledDatetime = Carbon::parse($data['appointment_date'] . ' ' . $time);

                $conflict = Appointment::where('doctor_id', $appointment->doctor_id)
                    ->where('scheduled_time', $scheduledDatetime)
                    ->where('id', '!=', $appointment->id)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'appointment_time' => ['This slot is already booked.'],
                    ]);
                }

                if ($appointment->slot_id) {
                    AppointmentSlot::where('id', $appointment->slot_id)
                        ->update(['status' => 'available']);
                }
                $newSlot = AppointmentSlot::firstOrCreate(
                    [
                        'doctor_id'  => $appointment->doctor_id,
                        'start_time' => $scheduledDatetime,
                    ],
                    [
                        'end_time' => (clone $scheduledDatetime)->addMinutes($appointment->duration_min),
                        'status'   => 'available',
                    ]
                );
                $newSlot->update(['status' => 'booked']);

                $data['slot_id']        = $newSlot->id;
                $data['scheduled_time'] = $scheduledDatetime;
                unset($data['appointment_date'], $data['appointment_time']);
            }

            $appointment->update($data);
    if (
    isset($data['status']) &&
    $data['status'] === 'confirmed'
) {

    $this->notificationService
        ->sendAppointmentNotification(

            $appointment
                ->patient
                ->user,

            'Appointment Approved',

            "Your appointment with Dr. "
            .$appointment->doctor->user->first_name
            ." has been approved.",

            true
        );

    // Notify doctor that their appointment is confirmed
    $this->notificationService->sendAppointmentNotification(
        $appointment->doctor->user,
        'Appointment Confirmed',
        'An appointment has been confirmed and is now on your schedule.',
        false
    );

    // ── Auto-create telehealth session on confirmation ───────────────
    if ($appointment->is_telehealth && ! $appointment->telehealthSession) {
        try {
            $this->telehealthService->autoCreateSession($appointment);
        } catch (\Throwable) {
            // Never block the confirmation if session creation fails
        }
    }
}
if (
    isset($data['status']) &&
    $data['status'] === 'cancelled'
) {

    $this->notificationService
        ->sendAppointmentNotification(

            $appointment
                ->patient
                ->user,

            'Appointment Cancelled',

            'Your appointment has been cancelled.',

            true
        );

    // Notify doctor that the appointment was cancelled
    $this->notificationService->sendAppointmentNotification(
        $appointment->doctor->user,
        'Appointment Cancelled',
        'An appointment scheduled with you has been cancelled.',
        false
    );

    // Notify receptionist and hospital admin about the cancellation
    $patientName = trim(($appointment->patient->user->first_name ?? '') . ' ' . ($appointment->patient->user->last_name ?? ''));
    $doctorName  = trim(($appointment->doctor->user->first_name ?? '') . ' ' . ($appointment->doctor->user->last_name ?? ''));
    $this->notificationService->sendStaffAppointmentNotification(
        $appointment,
        'Appointment Cancelled',
        "The appointment for {$patientName} with Dr. {$doctorName} has been cancelled."
    );
}

            return $appointment->fresh([
                'patient',
                'doctor.user',
                'hospital',
                'department',
                'approvedBy',
                'slot'
            ]);
        });
    }

    /**
     * Admin-only reschedule: allows reassigning a confirmed appointment
     * to a DIFFERENT doctor in the same hospital and department.
     * Used when the original doctor has an approved leave on that day.
     */
    public function adminReschedule(
        Appointment $appointment,
        string $slotId
    ): Appointment {
        return DB::transaction(function () use ($appointment, $slotId) {

            /*
            |------------------------------------------------------------------
            | 1. Only confirmed appointments can be admin-rescheduled
            |------------------------------------------------------------------
            */
            if ($appointment->status !== 'confirmed') {
                throw ValidationException::withMessages([
                    'appointment' => ['Only confirmed appointments can be reassigned.'],
                ]);
            }

            /*
            |------------------------------------------------------------------
            | 2. Load the target slot with its doctor
            |------------------------------------------------------------------
            */
            $slot = AppointmentSlot::with('doctor')
                ->findOrFail($slotId);

            /*
            |------------------------------------------------------------------
            | 3. Slot must be available
            |------------------------------------------------------------------
            */
            if ($slot->status !== 'available') {
                throw ValidationException::withMessages([
                    'slot' => ['The selected slot is not available.'],
                ]);
            }

            /*
            |------------------------------------------------------------------
            | 4. Slot cannot be in the past
            |------------------------------------------------------------------
            */
            if ($slot->start_time->isPast()) {
                throw ValidationException::withMessages([
                    'slot' => ['Cannot reassign to a past time slot.'],
                ]);
            }

            /*
            |------------------------------------------------------------------
            | 5. Replacement doctor must be in the same hospital
            |------------------------------------------------------------------
            */
            if ($slot->doctor->hospital_id !== $appointment->hospital_id) {
                throw ValidationException::withMessages([
                    'slot' => ['Replacement doctor belongs to a different hospital.'],
                ]);
            }

            /*
            |------------------------------------------------------------------
            | 6. Replacement doctor must be in the same department
            |------------------------------------------------------------------
            */
            if ($slot->doctor->department_id !== $appointment->department_id) {
                throw ValidationException::withMessages([
                    'slot' => ['Replacement doctor belongs to a different department.'],
                ]);
            }

            /*
            |------------------------------------------------------------------
            | 7. Patient must not have a conflicting appointment at the same time
            |------------------------------------------------------------------
            */
            $patientConflict = Appointment::where('patient_id', $appointment->patient_id)
                ->where('id', '!=', $appointment->id)
                ->where('scheduled_time', $slot->start_time)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->exists();

            if ($patientConflict) {
                throw ValidationException::withMessages([
                    'slot' => ['Patient already has another appointment at this time.'],
                ]);
            }

            /*
            |------------------------------------------------------------------
            | 8. Release the old slot
            |------------------------------------------------------------------
            */
            if ($appointment->slot_id) {
                AppointmentSlot::where('id', $appointment->slot_id)
                    ->update(['status' => 'available']);
            }

            /*
            |------------------------------------------------------------------
            | 9. Book the new slot
            |------------------------------------------------------------------
            */
            $slot->update(['status' => 'booked']);

            /*
            |------------------------------------------------------------------
            | 10. Update the appointment — new doctor, slot, and time
            |------------------------------------------------------------------
            */
            $appointment->update([
                'doctor_id'      => $slot->doctor_id,
                'slot_id'        => $slot->id,
                'scheduled_time' => $slot->start_time,
            ]);

            // Notify patient that their appointment was reassigned to a different doctor
            try {
                $oldDoctor = \App\Models\HealthcareProvider::with('user')->find($appointment->getOriginal('doctor_id'));
                $oldName   = $oldDoctor ? ($oldDoctor->user->first_name . ' ' . $oldDoctor->user->last_name) : 'previous doctor';
                $newName   = $slot->doctor->user->first_name . ' ' . $slot->doctor->user->last_name;
                app(\App\Services\NotificationService::class)->sendAdminRescheduleNotification(
                    $appointment->fresh(), $oldName, $newName
                );
            } catch (\Throwable) { /* silent — never block the reschedule */ }

            return $appointment->fresh([
                'patient',
                'doctor.user',
                'hospital',
                'department',
                'slot',
                'approvedBy',
            ]);
        });
    }

    public function delete(Appointment $appointment): void
    {
        DB::transaction(function () use ($appointment) {
            if ($appointment->slot_id) {
                AppointmentSlot::where('id', $appointment->slot_id)
                    ->update(['status' => 'available']);
            }
            $appointment->delete();
        });
    }

    /**
     * Hide an appointment from the patient's view without deleting the record.
     * Doctors and hospital admins still see it.
     */
    public function hideForPatient(Appointment $appointment): void
    {
        $appointment->update(['patient_hidden' => true]);
    }
    public function reschedule(
    Appointment $appointment,
    string $slotId
): Appointment {

    return DB::transaction(function () use ($appointment, $slotId) {

        /*
        |--------------------------------------------------------------------------
        | 1. Cannot reschedule completed or cancelled appointments
        |--------------------------------------------------------------------------
        */

        if (in_array($appointment->status, ['completed', 'cancelled', 'no_show'])) {

            throw ValidationException::withMessages([
                'appointment' => [
                    'This appointment cannot be rescheduled.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 4. Load slot
        |--------------------------------------------------------------------------
        */

        $slot = AppointmentSlot::with('doctor')
            ->findOrFail($slotId);

        /*
        |--------------------------------------------------------------------------
        | 5. Slot must belong to same doctor
        |--------------------------------------------------------------------------
        */

        if ($slot->doctor_id !== $appointment->doctor_id) {

            throw ValidationException::withMessages([
                'slot' => [
                    'The selected slot belongs to another doctor.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 6. Slot must be available
        |--------------------------------------------------------------------------
        */

        if ($slot->status !== 'available') {

            throw ValidationException::withMessages([
                'slot' => [
                    'The selected slot is not available.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 7. Slot cannot be in the past
        |--------------------------------------------------------------------------
        */

        if ($slot->start_time->isPast()) {

            throw ValidationException::withMessages([
                'slot' => [
                    'Cannot reschedule to a past time.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 8. Hospital validation
        |--------------------------------------------------------------------------
        */

        if ($slot->doctor->hospital_id !== $appointment->hospital_id) {

            throw ValidationException::withMessages([
                'slot' => [
                    'Selected slot belongs to another hospital.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 9. Duration validation
        |--------------------------------------------------------------------------
        */

        $slotMinutes = $slot->start_time
            ->diffInMinutes($slot->end_time);

        if ($slotMinutes != $appointment->duration_min) {

            throw ValidationException::withMessages([
                'slot' => [
                    'Selected slot duration does not match the appointment duration.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 10. Patient conflict
        |--------------------------------------------------------------------------
        */

        $patientConflict = Appointment::where('patient_id', $appointment->patient_id)
            ->where('id', '!=', $appointment->id)
            ->where('scheduled_time', $slot->start_time)
            ->whereNotIn('status', [
                'cancelled',
                'completed'
            ])
            ->exists();

        if ($patientConflict) {

            throw ValidationException::withMessages([
                'slot' => [
                    'Patient already has another appointment at this time.'
                ]
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 11. Release old slot
        |--------------------------------------------------------------------------
        */

        if ($appointment->slot_id) {

            AppointmentSlot::where('id', $appointment->slot_id)
                ->update([
                    'status' => 'available'
                ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 12. Book new slot
        |--------------------------------------------------------------------------
        */

        $slot->update([
            'status' => 'booked'
        ]);

        /*
        |--------------------------------------------------------------------------
        | 13. Update appointment
        |--------------------------------------------------------------------------
        */

        $appointment->update([

            'slot_id' => $slot->id,

            'scheduled_time' => $slot->start_time,

        ]);
$this->notificationService
    ->sendAppointmentNotification(

        $appointment
            ->patient
            ->user,

        'Appointment Rescheduled',

        'Your appointment date has been changed.',

        true
    );

        return $appointment->fresh([

            'patient',

            'doctor.user',

            'hospital',

            'department',

            'slot',

            'approvedBy',

        ]);

    });

}
}
