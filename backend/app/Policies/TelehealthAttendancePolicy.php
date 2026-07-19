<?php

namespace App\Policies;

use App\Models\TelehealthAttendance;
use App\Models\User;

class TelehealthAttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['platform_admin', 'hospital_admin', 'doctor', 'patient']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TelehealthAttendance $telehealthAttendance): bool
    {
        // Can view own attendance record, or doctor/admin of the session
        return $user->id === $telehealthAttendance->user_id
            || $user->hasAnyRole(['platform_admin', 'hospital_admin', 'doctor']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user (doctor or patient) can join a session
        return $user->hasAnyRole(['doctor', 'patient', 'hospital_admin', 'platform_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TelehealthAttendance $telehealthAttendance): bool
    {
        // Only the attendee themselves can mark themselves as left
        return $user->id === $telehealthAttendance->user_id
            || $user->hasAnyRole(['platform_admin', 'hospital_admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TelehealthAttendance $telehealthAttendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TelehealthAttendance $telehealthAttendance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TelehealthAttendance $telehealthAttendance): bool
    {
        return false;
    }
}
