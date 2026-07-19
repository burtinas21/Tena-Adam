<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SymptomAnalytic;

class SymptomAnalyticsPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['platform_admin', 'hospital_admin', 'doctor']);
    }

    public function view(User $user, SymptomAnalytic $analytics): bool
    {
        return in_array($user->role, ['platform_admin', 'hospital_admin', 'doctor']);
    }

    public function create(User $user): bool
    {
        return true; // Everyone can create analytics (patients selecting symptoms)
    }

    public function update(User $user, SymptomAnalytic $analytics): bool
    {
        return $user->isAdmin(); // Only admins
    }

    public function delete(User $user, SymptomAnalytic $analytics): bool
    {
        return $user->isAdmin();
    }
}
