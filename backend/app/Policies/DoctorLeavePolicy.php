<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DoctorLeave;

class DoctorLeavePolicy
{
    /**
     * View all leave requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_leave')
            || $user->hasPermission('view_doctors');
    }

    public function view(User $user, DoctorLeave $leave): bool
    {
        if (!$user->hasPermission('manage_leave') && !$user->hasPermission('view_doctors')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        if ($user->hasRole('hospital_admin')) {
            return $user->hospitalStaff()
                ->where('hospital_id', $leave->doctor->hospital_id)
                ->exists();
        }

        if ($user->hasRole('doctor')) {
            return $leave->doctor_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_leave');
    }

    public function update(User $user, DoctorLeave $leave): bool
    {
        if ($user->hasRole('platform_admin') && $user->hasPermission('manage_leave')) {
            return true;
        }

        if (
            $user->hasRole('doctor') &&
            $user->hasPermission('manage_leave') &&
            $leave->doctor_id === $user->id &&
            $leave->status === 'pending'
        ) {
            return true;
        }

        return false;
    }

    public function delete(User $user, DoctorLeave $leave): bool
    {
        if ($user->hasRole('platform_admin') && $user->hasPermission('manage_leave')) {
            return true;
        }

        if (
            $user->hasRole('doctor') &&
            $user->hasPermission('manage_leave') &&
            $leave->doctor_id === $user->id &&
            $leave->status === 'pending'
        ) {
            return true;
        }

        return false;
    }

    public function approve(User $user, DoctorLeave $leave): bool
    {
        if (!$user->hasRole('hospital_admin') || !$user->hasPermission('manage_leave')) {
            return false;
        }

        return $user->hospitalStaff()
            ->where('hospital_id', $leave->doctor->hospital_id)
            ->exists();
    }
}
