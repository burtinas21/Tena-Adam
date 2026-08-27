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
     *
     * Platform admins can view any log.
     * Hospital admins can only view logs that belong to their own hospital.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        if (! $user->hasPermission('view_audit_logs')) {
            return false;
        }

        // Platform admins see everything
        if ($user->hasRole('platform_admin')) {
            return true;
        }

        // Hospital admins: the log must belong to their active hospital
        $hospitalId = $user->hospitalStaff()
            ->where('is_active', true)
            ->value('hospital_id');

        return $hospitalId !== null && $auditLog->hospital_id === $hospitalId;
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
