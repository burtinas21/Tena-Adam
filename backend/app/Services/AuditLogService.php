<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Write an audit log entry, automatically resolving the tenant hospital_id
     * from the authenticated user's active hospital staff record.
     */
    public function log(
        string  $action,
        ?string $table   = null,
        ?string $id      = null,
        ?array  $details = null,
        ?string $hospitalId = null
    ): void {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // If caller did not explicitly pass a hospital_id, resolve it from
        // the authenticated user's active hospital staff record.
        if ($hospitalId === null && $user) {
            $hospitalId = $user->hospitalStaff()
                ->where('is_active', true)
                ->value('hospital_id');
        }

        AuditLog::create([
            'user_id'      => $user?->id,
            'hospital_id'  => $hospitalId,
            'action'       => $action,
            'target_table' => $table,
            'target_id'    => $id,
            'details'      => $details,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }
}
