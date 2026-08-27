<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\HospitalStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class HospitalStaffController extends Controller
{
    /**
     * Resolve the hospital ID for the currently authenticated user.
     * Works for hospital_admin and receptionist (both have a hospitalStaff record).
     */
    private function resolveHospitalId(): ?string
    {
        $user = auth()->user();

        if ($user->hasRole('platform_admin')) {
            return null; // platform admin has no single hospital
        }

        return $user->hospitalStaff()->value('hospital_id');
    }

    public function index(Request $request)
    {
        $user       = auth()->user();
        $filterRole = $request->query('role', 'hospital_admin');

        $query = User::whereHas('roles', function ($q) use ($filterRole) {
            $q->where('name', $filterRole);
        })->with('roles');

        if ($user->hasRole('platform_admin')) {
            // sees all — no additional scope
        } else {
            // hospital_admin and receptionist see only staff in their own hospital
            $hospitalId = $this->resolveHospitalId();
            if (!$hospitalId) {
                return response()->json(['data' => []]);
            }
            $query->whereHas('hospitalStaff', function ($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            });
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }


    public function update(Request $request, User $user)
    {
        // Ensure the target user belongs to the same hospital as the requester
        if (!auth()->user()->hasRole('platform_admin')) {
            $myHospitalId     = $this->resolveHospitalId();
            $targetHospitalId = $user->hospitalStaff()->value('hospital_id');

            if ($myHospitalId !== $targetHospitalId) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $request->validate([
            'first_name' => 'sometimes|required|string',
            'last_name'  => 'sometimes|required|string',
            'phone'      => 'nullable|string',
            'email'      => 'sometimes|required|email|unique:users,email,' . $user->id,
            'is_active'  => 'sometimes|boolean',
        ]);

        $user->update($request->only([
            'first_name', 'last_name', 'phone', 'email', 'is_active'
        ]));

        return response()->json([
            'message' => 'Staff updated successfully',
            'user'    => $user->fresh(),
        ]);
    }


    public function destroy(User $user)
    {
        // Ensure the target user belongs to the same hospital as the requester
        if (!auth()->user()->hasRole('platform_admin')) {
            $myHospitalId     = $this->resolveHospitalId();
            $targetHospitalId = $user->hospitalStaff()->value('hospital_id');

            if ($myHospitalId !== $targetHospitalId) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $user->roles()->detach();
        $user->hospitalStaff()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Staff deleted successfully',
        ]);
    }


    public function store(Request $request)
    {
        $authUser = auth()->user();

        // Determine which role to assign: 'hospital_admin' (default) or 'receptionist'
        $staffRole = $request->input('role', 'hospital_admin');
        if (!in_array($staffRole, ['hospital_admin', 'receptionist'])) {
            $staffRole = 'hospital_admin';
        }

        $request->validate([
            'first_name'  => 'required|string',
            'last_name'   => 'required|string',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'nullable|string',
            'hospital_id' => 'sometimes|nullable|exists:hospitals,id',
        ]);

        // Platform admin must supply a hospital_id; other roles use their own hospital
        if ($authUser->hasRole('platform_admin')) {
            $hospitalId = $request->input('hospital_id');
            if (!$hospitalId) {
                return response()->json(['message' => 'Please select a hospital.'], 422);
            }
        } else {
            $hospitalId = $this->resolveHospitalId();
            if (!$hospitalId) {
                return response()->json(['message' => 'Could not determine your hospital.'], 422);
            }
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => null,
            'is_active'  => false,
        ]);

        $role = Role::where('name', $staffRole)->first();
        $user->roles()->attach($role->id);

        HospitalStaff::create([
            'hospital_id' => $hospitalId,
            'user_id'     => $user->id,
            'position'    => $staffRole,
        ]);

        // Generate invitation token
        $plainToken = \Illuminate\Support\Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($plainToken), 'created_at' => now()]
        );

        // Audit log
        \App\Models\AuditLog::create([
            'user_id'      => auth()->id(),
            'hospital_id'  => $hospitalId,
            'action'       => 'invitation_sent',
            'target_table' => 'users',
            'target_id'    => $user->id,
            'details'      => ['email' => $user->email, 'hospital_id' => $hospitalId],
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);

        // Send invitation email
        \Illuminate\Support\Facades\Mail::to($user->email)->send(
            new \App\Mail\StaffInvitationMail($user, $plainToken)
        );

        $label = $staffRole === 'receptionist' ? 'Receptionist' : 'Hospital admin';

        return response()->json([
            'message' => "{$label} created successfully",
            'user'    => $user->load('roles'),
        ], 201);
    }
}
