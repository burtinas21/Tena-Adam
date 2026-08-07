<?php

namespace App\Policies;

use App\Models\Hospital;
use App\Models\User;

class HospitalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_hospitals');
    }

    public function view(User $user, Hospital $hospital): bool
    {
        if (!$user->hasPermission('view_hospitals')) {
            return false;
        }

        if ($user->hasRole('platform_admin') || $user->hasRole('patient')) {
            return true;
        }

        return $user->hospitals()->where('hospital_id', $hospital->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_hospitals');
    }

    public function update(User $user, Hospital $hospital): bool
    {
        return $user->hasPermission('update_hospitals');
    }

    public function delete(User $user, Hospital $hospital): bool
    {
        return $user->hasPermission('delete_hospitals');
    }
}
