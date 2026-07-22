<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display all audit logs with optional filters.
     *
     * Query params:
     *   search      – partial match on action or user name/email
     *   action      – exact action string filter
     *   target_table – module/table filter
     *   date_from   – ISO date, inclusive lower bound on created_at
     *   date_to     – ISO date, inclusive upper bound on created_at
     *   per_page    – items per page (default 20)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::with('user')->latest('created_at');

        // Free-text search across action, user name and email
        if ($search = $request->input('search')) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('action', 'like', $like)
                  ->orWhereHas('user', function ($u) use ($like) {
                      $u->where('first_name', 'like', $like)
                        ->orWhere('last_name',  'like', $like)
                        ->orWhere('email',       'like', $like);
                  });
            });
        }

        // Exact action filter
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Module / target_table filter
        if ($table = $request->input('target_table')) {
            $query->where('target_table', $table);
        }

        // Date range
        if ($from = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $perPage = (int) $request->input('per_page', 20);
        $perPage = min(max($perPage, 5), 100);

        $logs = $query->paginate($perPage)->withQueryString();

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
            ->paginate(20)
            ->withQueryString();

        return AuditLogResource::collection($logs);
    }

    /**
     * Return distinct action values for filter dropdowns.
     */
    public function actions()
    {
        $this->authorize('viewAny', AuditLog::class);

        $actions = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return response()->json(['data' => $actions]);
    }
}