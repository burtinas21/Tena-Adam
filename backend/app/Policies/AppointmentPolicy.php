<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_appointments');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if (!$user->hasPermission('view_appointments')) {
            return false;
        }

        // Platform admin sees all
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
        return $user->hasPermission('book_appointment');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('platform_admin') && $user->hasPermission('reschedule_appointment')) {
            return true;
        }

        if ($user->hasRole('hospital_admin') && $user->hasPermission('reschedule_appointment')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $appointment->hospital_id)
                ->exists();
        }

        if ($user->hasRole('doctor') && $user->hasPermission('approve_appointment')) {
            return $appointment->doctor_id === $user->id;
        }

        if ($user->hasRole('patient') && $user->hasPermission('reschedule_appointment')) {
            return $appointment->patient_id === $user->id
                && in_array($appointment->status, ['pending', 'pending_payment']);
        }

        return false;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('platform_admin') && $user->hasPermission('cancel_appointment')) {
            return true;
        }

        if ($user->hasRole('patient') && $user->hasPermission('cancel_appointment')) {
            return $appointment->patient_id === $user->id
                && in_array($appointment->status, ['completed', 'cancelled']);
        }

        return false;
    }

    /**
     * Only hospital admins (of the same hospital) may reassign
     * a leave-affected appointment to a different doctor.
     */
    public function adminReschedule(User $user, Appointment $appointment): bool
    {
        if (!$user->hasPermission('reschedule_appointment')) {
            return false;
        }

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
