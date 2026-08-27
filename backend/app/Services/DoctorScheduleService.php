<?php

namespace App\Services;

use App\Models\DoctorSchedule;
use App\Models\HealthcareProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\NotificationService;

class DoctorScheduleService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Normalize a time value to H:i (24-hour) format.
     * Handles "08:30", "08:30:00", "08:30 AM", "4:30 PM", etc.
     */
    private function normalizeTime(?string $time): ?string
    {
        if (!$time) return null;

        $time = trim($time);

        // Already HH:MM or HH:MM:SS
        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $time, $m)) {
            return str_pad($m[1], 2, '0', STR_PAD_LEFT) . ':' . $m[2];
        }

        // 12-hour with AM/PM: "8:30 AM", "04:30 PM"
        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?\s*(AM|PM)$/i', $time, $m)) {
            $hour   = (int) $m[1];
            $minute = $m[2];
            $period = strtoupper($m[3]);

            if ($period === 'AM') {
                $hour = ($hour === 12) ? 0 : $hour;
            } else {
                $hour = ($hour !== 12) ? $hour + 12 : 12;
            }

            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . $minute;
        }

        return substr($time, 0, 5);
    }
    public function create(array $data)
{
    return DB::transaction(function () use ($data) {

        $user = auth()->user();

        // Hospital admin creates schedule for a doctor they manage
        if ($user->hasRole('hospital_admin')) {

            $doctorId   = $data['doctor_id']
                ?? null;

            if (!$doctorId) {
                throw ValidationException::withMessages([
                    'doctor_id' => 'doctor_id is required when creating a schedule as hospital_admin.'
                ]);
            }

            $hospitalId = $user->hospitalStaff()->value('hospital_id');

            // Verify doctor belongs to this hospital
            $provider = HealthcareProvider::where('id', $doctorId)
                ->where('hospital_id', $hospitalId)
                ->first();

            if (!$provider) {
                throw ValidationException::withMessages([
                    'doctor_id' => 'Doctor does not belong to your hospital.'
                ]);
            }

        } elseif ($user->hasRole('doctor')) {

            $doctorId   = $user->id;
            $hospitalId = $user->hospitalStaff()->value('hospital_id');

            if (!$hospitalId) {
                throw ValidationException::withMessages([
                    'hospital' => 'Doctor not assigned to hospital'
                ]);
            }

        } else {
            throw ValidationException::withMessages([
                'auth' => 'Only doctors or hospital admins can manage schedules'
            ]);
        }

        $exists = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $data['day_of_week'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'day_of_week' => 'Schedule already exists for this day'
            ]);
        }

       $schedule = DoctorSchedule::create([
    'doctor_id'         => $doctorId,
    'day_of_week'       => $data['day_of_week'],
    'start_time'        => $this->normalizeTime($data['start_time']),
    'end_time'          => $this->normalizeTime($data['end_time']),
    'slot_duration_min' => $data['slot_duration_min'] ?? 30,
    'lunch_start'       => $this->normalizeTime($data['lunch_start'] ?? null),
    'lunch_end'         => $this->normalizeTime($data['lunch_end'] ?? null),
    'is_available'      => $data['is_available'] ?? true,
]);

$schedule->load('doctor.user');

$this->notificationService->sendDoctorScheduleNotification(
    $schedule,
    'created'
);

return $schedule;
    });
}

   public function update(DoctorSchedule $schedule, array $data)
{
    return DB::transaction(function () use ($schedule, $data) {

        $user = auth()->user();

        // Only allow update if the schedule's doctor belongs to the admin's hospital
        if ($user->hasRole('hospital_admin')) {

            $hospitalId       = (string) $user->hospitals()->pluck('hospitals.id')->first();
            $doctorHospitalId = (string) ($schedule->doctor?->hospital_id ?? '');

            if ($doctorHospitalId !== $hospitalId) {
                throw ValidationException::withMessages([
                    'schedule' => 'Unauthorized: doctor does not belong to your hospital.'
                ]);
            }

        } elseif ($user->hasRole('doctor')) {

            if ((string) $schedule->doctor_id !== (string) $user->id) {
                throw ValidationException::withMessages([
                    'schedule' => 'Unauthorized: you can only update your own schedule.'
                ]);
            }

        }

        // Only update allowed fields — never touch doctor_id or day_of_week
        $allowed = [];
        foreach (['start_time', 'end_time', 'slot_duration_min', 'lunch_start', 'lunch_end', 'is_available'] as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                // Normalize time fields to H:i (24-hour) format
                if (in_array($field, ['start_time', 'end_time', 'lunch_start', 'lunch_end'])) {
                    $value = $this->normalizeTime($value);
                }
                $allowed[$field] = $value;
            }
        }

        if (!empty($allowed)) {
            $schedule->update($allowed);
        }

        $schedule = $schedule->fresh(['doctor.user']);

$this->notificationService->sendDoctorScheduleNotification(
    $schedule,
    'updated'
);

return $schedule;
    });
}

    public function delete(DoctorSchedule $schedule)
    {
        return DB::transaction(function () use ($schedule) {

            $hospitalId = auth()->user()
                ->hospitalStaff()
                ->value('hospital_id');

            if ($schedule->doctor->hospital_id !== $hospitalId) {
                throw ValidationException::withMessages([
                    'schedule' => 'Unauthorized delete'
                ]);
            }

            // return $schedule->delete();
             $schedule->load('doctor.user');

$this->notificationService->sendDoctorScheduleNotification(
    $schedule,
    'deleted'
);

$schedule->forceDelete();

return true;
        });
    }

    public function getByDoctor($doctorId)
    {
        return DoctorSchedule::where('doctor_id', $doctorId)
            ->orderBy('day_of_week')
            ->get();
    }
}