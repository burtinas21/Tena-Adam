<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * View all audit logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('platform_admin')
            || $user->hasRole('hospital_admin');
    }

    /**
     * View a single audit log.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasRole('platform_admin')
            || $user->hasRole('hospital_admin');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AuditLog $auditLog): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $auditLog): bool
    {
        return false;
    }
}