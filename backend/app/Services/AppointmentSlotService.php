<?php

namespace App\Services;

use App\Models\AppointmentSlot;
use App\Models\DoctorLeave;
use App\Models\DoctorSchedule;
use App\Models\HealthcareProvider;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentSlotService
{public function generateSlots(
    HealthcareProvider $doctor,
    Carbon $date
)
{
    return DB::transaction(function () use ($doctor, $date) {

        /*
        |--------------------------------------------------------------------------
        | 1. Check approved leave
        |--------------------------------------------------------------------------
        */

        $leave = DoctorLeave::where('doctor_id', $doctor->id)
            ->whereDate('leave_date', $date)
            ->where('status', 'approved')
            ->exists();

        if ($leave) {

            throw ValidationException::withMessages([

                'leave' => [
                    'Doctor is on leave for this date.'
                ]

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 2. Get doctor's schedule
        |--------------------------------------------------------------------------
        */

        $schedule = DoctorSchedule::where(

            'doctor_id',
            $doctor->id

        )
        ->where(

            'day_of_week',
            $date->dayOfWeek

        )
        ->where(

            'is_available',
            true

        )
        ->first();

        if (! $schedule) {

            throw ValidationException::withMessages([

                'schedule' => [
                    'Doctor has no schedule for this day.'
                ]

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | 3. Generate slots
        |--------------------------------------------------------------------------
        */

        $this->createSlots(
            $doctor,
            $schedule,
            $date
        );

        return AppointmentSlot::where(
                'doctor_id',
                $doctor->id
            )
            ->whereDate(
                'start_time',
                $date
            )
            ->orderBy('start_time')
            ->get();

    });
}
private function createSlots(
    HealthcareProvider $doctor,
    DoctorSchedule $schedule,
    Carbon $date
): void
{

    $current = Carbon::parse(

        $date->toDateString() .
        ' ' .
        $schedule->start_time

    );

    $end = Carbon::parse(

        $date->toDateString() .
        ' ' .
        $schedule->end_time

    );

    while ($current < $end) {

        $slotEnd = $current
            ->copy()
            ->addMinutes(
                $schedule->slot_duration_min
            );

        /*
        |--------------------------------------------------------------------------
        | Skip lunch
        |--------------------------------------------------------------------------
        */

        if (

            $this->isLunchTime(
                $current,
                $schedule
            )

        ) {

            $current = Carbon::parse(

                $date->toDateString()
                . ' '
                . $schedule->lunch_end

            );

            continue;

        }

        /*
        |--------------------------------------------------------------------------
        | Skip duplicate slot
        |--------------------------------------------------------------------------
        */

        if (

            ! $this->slotExists(
                $doctor->id,
                $current
            )

        ) {

            AppointmentSlot::create([

                'doctor_id' => $doctor->id,

                'start_time' => $current,

                'end_time' => $slotEnd,

                'status' => 'available',

            ]);

        }

        $current = $slotEnd;

    }

}
private function isLunchTime(
    Carbon $time,
    DoctorSchedule $schedule
): bool
{

    if (

        ! $schedule->lunch_start ||

        ! $schedule->lunch_end

    ) {

        return false;

    }

    $lunchStart = Carbon::parse(

        $time->toDateString()
        .' '.
        $schedule->lunch_start

    );

    $lunchEnd = Carbon::parse(

        $time->toDateString()
        .' '.
        $schedule->lunch_end

    );

    return $time >= $lunchStart
        && $time < $lunchEnd;

}
private function slotExists(
    string $doctorId,
    Carbon $startTime
): bool
{

    return AppointmentSlot::where(

        'doctor_id',
        $doctorId

    )
    ->where(

        'start_time',
        $startTime

    )
    ->exists();

}
public function blockSlotsForLeave(
    HealthcareProvider $doctor,
    Carbon $date
): int
{
    return DB::transaction(function () use ($doctor, $date) {

        return AppointmentSlot::where('doctor_id', $doctor->id)
            ->whereDate('start_time', $date)
            ->where('status', 'available') // ONLY AVAILABLE
            ->update([
                'status' => 'blocked'
            ]);

    });
}
public function bookSlot(
    AppointmentSlot $slot
): AppointmentSlot
{
    return DB::transaction(function () use ($slot) {

        // 1. Check availability
        if ($slot->status !== 'available') {

            throw ValidationException::withMessages([
                'slot' => 'This slot is not available'
            ]);

        }

        // 2. Mark as booked
        $slot->update([
            'status' => 'booked'
        ]);

        return $slot->fresh();

    });
}
public function releaseSlot(
    AppointmentSlot $slot
): AppointmentSlot
{
    return DB::transaction(function () use ($slot) {

        // Only release if currently booked
        if ($slot->status !== 'booked') {

            throw ValidationException::withMessages([
                'slot' => 'Only booked slots can be released'
            ]);

        }

        // Reset slot
        $slot->update([
            'status' => 'available',
            'appointment_id' => null
        ]);

        return $slot->fresh();

    });
}
public function completeSlot(
    AppointmentSlot $slot
): AppointmentSlot
{
    return DB::transaction(function () use ($slot) {

        if ($slot->status !== 'booked') {

            throw ValidationException::withMessages([
                'slot' => 'Only booked slots can be completed'
            ]);

        }

        $slot->update([
            'status' => 'completed'
        ]);

        return $slot->fresh();

    });
}public function syncWithAppointment($appointment): void
{
    DB::transaction(function () use ($appointment) {

        $slot = AppointmentSlot::find(
            $appointment->slot_id
        );

        if (! $slot) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | If appointment is confirmed → mark slot booked
        |--------------------------------------------------------------------------
        */

        if ($appointment->status === 'confirmed') {

            $slot->update([
                'status' => 'booked'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | If appointment completed → mark slot completed
        |--------------------------------------------------------------------------
        */

        if ($appointment->status === 'completed') {

            $slot->update([
                'status' => 'completed'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | If appointment cancelled → release slot
        |--------------------------------------------------------------------------
        */

        if ($appointment->status === 'cancelled') {

            $slot->update([
                'status' => 'available',
                'appointment_id' => null
            ]);
        }

    });
}}