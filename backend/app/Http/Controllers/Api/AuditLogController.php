<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display all audit logs.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $logs = AuditLog::with('user')
            ->latest('created_at')
            ->paginate(20);

        return AuditLogResource::collection($logs);
    }

    /**
     * Display one audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $this->authorize('view', $auditLog);

        $auditLog->load('user');

        return new AuditLogResource($auditLog);
    }

    /**
     * Display logs for a specific user.
     */
    public function userLogs(string $userId)
    {
        $this->authorize('viewAny', AuditLog::class);

        $logs = AuditLog::with('user')
            ->where('user_id', $userId)
            ->latest('created_at')
            ->paginate(20);

        return AuditLogResource::collection($logs);
    }
}