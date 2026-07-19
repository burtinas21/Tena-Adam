<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SymptomDepartmentMapping;

class SymptomDepartmentMappingPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view mappings
    }

    public function view(User $user, SymptomDepartmentMapping $mapping): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins
    }

    public function update(User $user, SymptomDepartmentMapping $mapping): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SymptomDepartmentMapping $mapping): bool
    {
        return $user->isAdmin();
    }
}
