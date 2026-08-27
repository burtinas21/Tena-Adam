<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Resolve the hospital_id that should be used to scope audit log queries.
     *
     * - Platform admins:  null  → see all logs (no scope applied)
     * - Hospital admins:  their hospital_id → tenant-isolated view
     */
    private function scopeHospitalId(): ?string
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Platform admins see everything
        if ($user->hasRole('platform_admin')) {
            return null;
        }

        // Everyone else is scoped to their active hospital
        return $user->hospitalStaff()
            ->where('is_active', true)
            ->value('hospital_id');
    }

    /**
     * Apply hospital scoping to a query builder when a hospital_id is present.
     * When hospitalId is null the query is unrestricted (platform admin case).
     */
    private function applyHospitalScope($query, ?string $hospitalId)
    {
        if ($hospitalId !== null) {
            $query->where('hospital_id', $hospitalId);
        }

        return $query;
    }

    /**
     * Display paginated audit logs with optional filters.
     *
     * Query params:
     *   search       – partial match on action or user name/email
     *   action       – exact action string filter
     *   target_table – module/table filter
     *   date_from    – ISO date, inclusive lower bound on created_at
     *   date_to      – ISO date, inclusive upper bound on created_at
     *   per_page     – items per page (default 20)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $hospitalId = $this->scopeHospitalId();

        $query = AuditLog::with('user')->latest('created_at');

        // Tenant isolation — hospital admins only see their own hospital's logs
        $this->applyHospitalScope($query, $hospitalId);

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
     * Display one audit log — enforce tenant ownership for hospital admins.
     */
    public function show(AuditLog $auditLog)
    {
        $this->authorize('view', $auditLog);

        $hospitalId = $this->scopeHospitalId();

        // Hospital admins must not access logs belonging to another hospital
        if ($hospitalId !== null && $auditLog->hospital_id !== $hospitalId) {
            abort(403, 'This audit log does not belong to your hospital.');
        }

        $auditLog->load('user');

        return new AuditLogResource($auditLog);
    }

    /**
     * Display logs for a specific user (scoped to tenant).
     */
    public function userLogs(string $userId)
    {
        $this->authorize('viewAny', AuditLog::class);

        $hospitalId = $this->scopeHospitalId();

        $query = AuditLog::with('user')
            ->where('user_id', $userId)
            ->latest('created_at');

        $this->applyHospitalScope($query, $hospitalId);

        $logs = $query->paginate(20)->withQueryString();

        return AuditLogResource::collection($logs);
    }

    /**
     * Return distinct action values for filter dropdowns (tenant-scoped).
     */
    public function actions()
    {
        $this->authorize('viewAny', AuditLog::class);

        $hospitalId = $this->scopeHospitalId();

        $query = AuditLog::select('action')->distinct()->orderBy('action');

        $this->applyHospitalScope($query, $hospitalId);

        $actions = $query->pluck('action');

        return response()->json(['data' => $actions]);
    }
}
