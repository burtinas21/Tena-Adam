<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Symptom;

class SymptomPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view symptoms
    }

    public function view(User $user, Symptom $symptom): bool
    {
        return true; // Everyone can view a symptom
    }

    public function create(User $user): bool
    {
        return $user->isAdmin(); // Platform + Hospital admins
    }

    public function update(User $user, Symptom $symptom): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Symptom $symptom): bool
    {
        return $user->isAdmin();
    }
}
