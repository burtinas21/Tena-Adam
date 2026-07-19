<?php

namespace App\Services;

use App\Models\DoctorLeave;
use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\DB;
use App\Services\AppointmentSlotService;
use App\Models\HealthcareProvider;
use App\Models\Appointment;
use Illuminate\Validation\ValidationException;
use App\Services\NotificationService;

class DoctorLeaveService
{
    public function __construct(
        private AppointmentSlotService $slotService,
         private NotificationService $notificationService
    ) {
    }
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $user = auth()->user();

            // Only doctors can create leave requests.
            if (!$user->hasRole('doctor')) {

                throw ValidationException::withMessages([
                    'doctor' => [
                        'Only doctors can request leave.'
                    ]
                ]);

            }

            $doctorId = $user->id;

            // Prevent duplicate leave for the same date.
            $exists = DoctorLeave::where(
                'doctor_id',
                $doctorId
            )
            ->where(
                'leave_date',
                $data['leave_date']
            )
            ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'leave_date' => [
                        'You already requested leave for this date.'
                    ]
                ]);

            }

            $leave = DoctorLeave::create([

                'doctor_id' => $doctorId,

                'leave_date' => $data['leave_date'],

                'leave_type' => $data['leave_type'],

                'reason' => $data['reason'] ?? null,

                'status' => 'pending',

            ]);
            $leave = $leave->load('doctor.user');

$this->notificationService->sendDoctorLeaveNotification(
    $leave,
    'submitted'
);

return $leave;

            // return $leave->load('doctor.user');

        });
    }

    public function update(
        DoctorLeave $leave,
        array $data
    )
    {
        return DB::transaction(function () use (
            $leave,
            $data
        ) {

            if ($leave->status !== 'pending') {

                throw ValidationException::withMessages([
                    'status' => [
                        'Only pending leave requests can be updated.'
                    ]
                ]);

            }

            $exists = DoctorLeave::where(
                'doctor_id',
                $leave->doctor_id
            )
            ->where(
                'leave_date',
                $data['leave_date'] ?? $leave->leave_date
            )
            ->where(
                'id',
                '!=',
                $leave->id
            )
            ->exists();

            if ($exists) {

                throw ValidationException::withMessages([
                    'leave_date' => [
                        'Leave already exists for this date.'
                    ]
                ]);

            }

            $leave->update($data);

            return $leave->fresh()->load('doctor.user');

        });
    }
   public function approve(
    DoctorLeave $leave,
    string $status
)
{
    return DB::transaction(function () use ($leave, $status) {

        if (! in_array($status, ['approved', 'rejected'])) {

            throw ValidationException::withMessages([
                'status' => ['Invalid status.']
            ]);

        }

        if ($leave->status !== 'pending') {

            throw ValidationException::withMessages([
                'status' => [
                    'Leave request has already been processed.'
                ]
            ]);

        }

        $leave->update([

            'status' => $status,

            'approved_by' => auth()->id(),

        ]);
        $this->notificationService->sendDoctorLeaveNotification(
      $leave->fresh(['doctor.user']),
      $status
);

        if ($status !== 'approved') {

            return [

                'leave' => $leave->fresh([
                    'doctor.user',
                    'approvedBy'
                ]),

                'blocked_slots' => 0,

                'appointments_to_reschedule' => 0,

                'warning' => null,

                'appointments' => collect(),

            ];

        }

        $doctor = HealthcareProvider::findOrFail(
            $leave->doctor_id
        );

        /*
        |--------------------------------------------------------------------------
        | Block ONLY available slots
        |--------------------------------------------------------------------------
        */

        $blockedSlots = $this->slotService
            ->blockSlotsForLeave(
                $doctor,
                $leave->leave_date
            );

        /*
        |--------------------------------------------------------------------------
        | Find confirmed appointments
        |--------------------------------------------------------------------------
        */

        $appointments = Appointment::with([
                'patient',
                'doctor.user',
                'slot'
            ])
            ->where('doctor_id', $doctor->id)
            ->whereDate(
                'scheduled_time',
                $leave->leave_date
            )
            ->where('status', 'confirmed')
            ->get();

        return [

            'leave' => $leave->fresh([
                'doctor.user',
                'approvedBy'
            ]),

            'blocked_slots' => $blockedSlots,

            'appointments_to_reschedule'
                => $appointments->count(),

            'warning'
                => $appointments->count() > 0
                ? 'Doctor has confirmed appointments that require rescheduling.'
                : null,

            'appointments'
                => $appointments,

        ];

    });
}

    public function delete(
        DoctorLeave $leave
    )
    {
        return DB::transaction(function () use ($leave) {

            if ($leave->status !== 'pending') {

                throw ValidationException::withMessages([
                    'status' => [
                        'Processed leave requests cannot be deleted.'
                    ]
                ]);

            }

            $leave->delete();

            return true;

        });
    }
}