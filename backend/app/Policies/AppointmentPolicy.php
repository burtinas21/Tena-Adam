<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('platform_admin')
            || $user->hasRole('hospital_admin')
            || $user->hasRole('doctor')
            || $user->hasRole('patient')
            || $user->hasRole('receptionist');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('platform_admin')) return true;

        if ($user->hasRole('hospital_admin') || $user->hasRole('receptionist')) {
            $hospitalId = $user->hospitalStaff()->value('hospital_id');
            return $hospitalId === $appointment->hospital_id;
        }

        if ($user->hasRole('doctor')) {
            return $appointment->doctor_id === $user->id;
        }

        if ($user->hasRole('patient')) {
            return $appointment->patient_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('patient')
            || $user->hasRole('receptionist')
            || $user->hasRole('platform_admin');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $appointment->hospital_id)
                ->exists();
        }

        if ($user->hasRole('doctor')) {
           
            return $appointment->doctor_id === $user->id;
        }

        if ($user->hasRole('patient')) {
           
            return $appointment->patient_id === $user->id
                && $appointment->status === 'pending';
        }

        return false;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('platform_admin');
    }

    /**
     * Only hospital admins (of the same hospital) may reassign
     * a leave-affected appointment to a different doctor.
     */
    public function adminReschedule(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $appointment->hospital_id)
                ->exists();
        }

        return false;
    }
}