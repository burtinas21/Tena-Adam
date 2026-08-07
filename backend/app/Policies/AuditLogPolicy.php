<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * View all audit logs.
     * Checks the actual permission so that removing view_audit_logs
     * from a role via the Roles & Permissions panel is honoured.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_audit_logs');
    }

    /**
     * View a single audit log.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasPermission('view_audit_logs');
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