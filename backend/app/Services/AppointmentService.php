<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\DoctorSchedule;
use App\Models\HealthcareProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    private function normalizeTime(string $time): string
    {
        return substr($time, 0, 5);
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
            $query->where('patient_id', $user->id);
        } else {
            return collect();
        }

        return $query->get();
    }

    public function create(array $data): Appointment
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

            $exists = Appointment::where('doctor_id', $doctor->id)
                ->where('scheduled_time', $scheduledDatetime)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'appointment_time' => ['This appointment slot is already booked.'],
                ]);
            }

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

            $slot->update(['status' => 'booked']);

   
            $appointment = Appointment::create([
                'patient_id'    => $patient->id,
                'doctor_id'     => $doctor->id,                'hospital_id'   => $hospitalId,
                'department_id' => $departmentId,
                'slot_id'       => $slot->id,
                'scheduled_time'=> $scheduledDatetime,
                'duration_min'  => $schedule->slot_duration_min,
                'status'        => 'pending',
                'reason'        => $data['reason'],
                'notes'         => $data['notes'] ?? null,
                'is_telehealth' => $data['is_telehealth'] ?? false,
            ]);

            return $appointment->load([
                'patient',
                'doctor.user',
                'hospital',
                'department',
                'approvedBy',
                'slot',
            ]);
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

        /*
        |--------------------------------------------------------------------------
        | 14. Return
        |--------------------------------------------------------------------------
        */

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
