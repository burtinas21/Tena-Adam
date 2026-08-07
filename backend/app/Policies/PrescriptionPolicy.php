<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_prescription');
    }

    public function view(User $user, Prescription $prescription): bool
    {
        if (!$user->hasPermission('view_prescription')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) return true;

        if ($user->hasRole('hospital_admin') || $user->hasRole('receptionist')) {
            $hospitalId = $user->hospitalStaff()->value('hospital_id');
            return $hospitalId === $prescription->encounter?->hospital_id;
        }

        if ($user->hasRole('doctor')) {
            return $prescription->encounter?->doctor_id === $user->healthcareProvider?->id;
        }

        if ($user->hasRole('patient')) {
            return $prescription->encounter?->patient_id === $user->patient?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_prescription');
    }

    public function update(User $user, Prescription $prescription): bool
    {
        if (!$user->hasPermission('create_prescription')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) return true;

        if ($user->hasRole('doctor')) {
            return $prescription->encounter?->doctor_id === $user->healthcareProvider?->id
                && $prescription->status === 'active';
        }

        return false;
    }

    public function delete(User $user, Prescription $prescription): bool
    {
        if (!$user->hasPermission('create_prescription')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) return true;

        if ($user->hasRole('doctor')) {
            return $prescription->encounter?->doctor_id === $user->healthcareProvider?->id
                && $prescription->status === 'active';
        }

        return false;
    }

    public function complete(User $user, Prescription $prescription): bool
    {
        if (!$user->hasPermission('create_prescription')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) return true;

        if ($user->hasRole('doctor')) {
            return $prescription->encounter?->doctor_id === $user->healthcareProvider?->id
                && $prescription->status === 'active';
        }

        return false;
    }
}
