<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_departments');
    }

    public function view(User $user, Department $department): bool
    {
        if (!$user->hasPermission('view_departments')) {
            return false;
        }

        if ($user->hasRole('platform_admin') || $user->hasRole('doctor')
            || $user->hasRole('receptionist') || $user->hasRole('patient')) {
            return true;
        }

        return $user->hospitals()
            ->where('hospitals.id', $department->hospital_id)
            ->exists();
    }

    public function create(User $user, Department $department): bool
    {
        if (!$user->hasPermission('create_departments')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where('hospitals.id', $department->hospital_id)
            ->exists();
    }

    public function update(User $user, Department $department): bool
    {
        if (!$user->hasPermission('update_departments')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where('hospitals.id', $department->hospital_id)
            ->exists();
    }

    public function delete(User $user, Department $department): bool
    {
        if (!$user->hasPermission('delete_departments')) {
            return false;
        }

        if ($user->hasRole('platform_admin')) {
            return true;
        }

        return $user->hospitals()
            ->where('hospitals.id', $department->hospital_id)
            ->exists();
    }
}
