<?php

namespace App\Services;

use App\Models\DoctorSchedule;
use App\Models\HealthcareProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DoctorScheduleService
{
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

        return DoctorSchedule::create([
            'doctor_id'         => $doctorId,
            'day_of_week'       => $data['day_of_week'],
            'start_time'        => $data['start_time'],
            'end_time'          => $data['end_time'],
            'slot_duration_min' => $data['slot_duration_min'] ?? 30,
            'lunch_start'       => $data['lunch_start'] ?? null,
            'lunch_end'         => $data['lunch_end'] ?? null,
            'is_available'      => $data['is_available'] ?? true,
        ]);
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
                $allowed[$field] = $data[$field];
            }
        }

        if (!empty($allowed)) {
            $schedule->update($allowed);
        }

        return $schedule->fresh(['doctor.user']);
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
             return $schedule->forceDelete();
        });
    }

    public function getByDoctor($doctorId)
    {
        return DoctorSchedule::where('doctor_id', $doctorId)
            ->orderBy('day_of_week')
            ->get();
    }
}