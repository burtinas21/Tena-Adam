<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Return the hospital_id the authenticated hospital_admin belongs to.
     * Returns null for platform_admin or if no active staff record exists.
     */
    private function adminHospitalId(Request $request): ?string
    {
        $staff = $request->user()
            ->hospitalStaff()
            ->where('is_active', true)
            ->first();

        return $staff?->hospital_id;
    }

    /**
     * Guard: ensure the current user is allowed to operate on this role.
     *  - platform_admin  → may only touch global roles (hospital_id IS NULL)
     *  - hospital_admin  → may only touch roles that belong to their hospital
     */
    private function authorizeRoleAccess(Request $request, Role $role): void
    {
        $user = $request->user();

        if ($user->hasRole('hospital_admin')) {
            $hospitalId = $this->adminHospitalId($request);
            if ($role->hospital_id !== $hospitalId) {
                abort(403, 'You do not have access to this role.');
            }
        } elseif ($user->hasRole('platform_admin')) {
            if (! is_null($role->hospital_id)) {
                abort(403, 'Platform admins can only manage global roles.');
            }
        }
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    /**
     * GET /roles
     *
     * platform_admin → only global roles (hospital_id IS NULL)
     * hospital_admin → only roles scoped to their hospital
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('hospital_admin')) {
            $hospitalId = $this->adminHospitalId($request);

            if (! $hospitalId) {
                return response()->json(
                    ['message' => 'No hospital associated with your account.'],
                    403
                );
            }

            $roles = Role::with('permissions')
                ->where('hospital_id', $hospitalId)
                ->orderBy('name')
                ->get();

            return response()->json(['data' => $roles]);
        }

        // platform_admin — global roles only
        $roles = Role::with('permissions')
            ->whereNull('hospital_id')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $roles]);
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    /**
     * POST /roles
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('hospital_admin')) {
            $hospitalId = $this->adminHospitalId($request);

            if (! $hospitalId) {
                return response()->json(
                    ['message' => 'No hospital associated with your account.'],
                    403
                );
            }

            $validated = $request->validate([
                'name' => [
                    'required', 'string', 'max:50',
                    Rule::unique('roles')->where(
                        fn ($q) => $q->where('hospital_id', $hospitalId)
                    ),
                ],
                'description' => ['nullable', 'string', 'max:255'],
            ]);

            if (Str::startsWith(strtolower($validated['name']), 'platform')) {
                return response()->json(
                    ['message' => 'Hospital admins cannot create platform-level roles.'],
                    403
                );
            }

            $role = Role::create([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'hospital_id' => $hospitalId,
                'is_default'  => false,
            ]);

            return response()->json(['data' => $role->load('permissions')], 201);
        }

        // platform_admin creates global roles
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('roles')->whereNull('hospital_id'),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $role = Role::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'hospital_id' => null,
            'is_default'  => false,
        ]);

        return response()->json(['data' => $role->load('permissions')], 201);
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Request $request, Role $role)
    {
        $this->authorizeRoleAccess($request, $role);
        return response()->json(['data' => $role->load('permissions')]);
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, Role $role)
    {
        $this->authorizeRoleAccess($request, $role);

        $systemRoles = ['platform_admin', 'hospital_admin', 'doctor', 'receptionist', 'patient'];
        if (in_array($role->name, $systemRoles)) {
            return response()->json(['message' => 'System roles cannot be renamed.'], 422);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes', 'string', 'max:50',
                Rule::unique('roles')->where(function ($q) use ($role) {
                    return is_null($role->hospital_id)
                        ? $q->whereNull('hospital_id')
                        : $q->where('hospital_id', $role->hospital_id);
                })->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $role->update($validated);
        return response()->json(['data' => $role->load('permissions')]);
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Request $request, Role $role)
    {
        $this->authorizeRoleAccess($request, $role);

        $protectedRoles = ['platform_admin', 'hospital_admin', 'doctor', 'receptionist', 'patient'];
        if (in_array($role->name, $protectedRoles)) {
            return response()->json(['message' => 'System roles cannot be deleted.'], 422);
        }

        $role->delete();
        return response()->json(['message' => 'Role deleted successfully.']);
    }

    // ── Sync Permissions ──────────────────────────────────────────────────────

    /**
     * PUT /roles/{role}/permissions
     * Body: { "permission_ids": ["uuid", ...] }
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $this->authorizeRoleAccess($request, $role);

        $validated = $request->validate([
            'permission_ids'   => ['required', 'array'],
            'permission_ids.*' => ['uuid', 'exists:permissions,id'],
        ]);

        // Hospital admins cannot assign restricted permissions
        if ($request->user()->hasRole('hospital_admin')) {
            $restricted = ['delete_hospitals', 'create_hospitals', 'view_audit_logs'];
            $hasForbidden = Permission::whereIn('id', $validated['permission_ids'])
                ->whereIn('name', $restricted)
                ->exists();

            if ($hasForbidden) {
                return response()->json(
                    ['message' => 'You cannot assign restricted permissions.'],
                    403
                );
            }
        }

        $role->permissions()->sync($validated['permission_ids']);
        return response()->json(['data' => $role->load('permissions')]);
    }

    // ── Users on a role ───────────────────────────────────────────────────────

    public function users(Request $request, Role $role)
    {
        $this->authorizeRoleAccess($request, $role);

        $users = $role->users()
            ->select(['users.id', 'first_name', 'last_name', 'email'])
            ->get();

        return response()->json(['data' => $users]);
    }
}
