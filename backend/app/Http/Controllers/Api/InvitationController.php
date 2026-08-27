<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InvitationController extends Controller
{

    public function check(Request $request)
{
    $request->validate([
        'token' => 'required|string',
        'email' => 'required|email',
    ]);

    $record = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->first();

    if (! $record || ! Hash::check($request->token, $record->token)) {
        return response()->json([
            'valid' => false,
            'message' => 'Invalid or expired invitation link.',
        ], 422);
    }

    // Expire after 24 hours
    if (now()->diffInHours($record->created_at) >= 24) {
        return response()->json([
            'valid' => false,
            'message' => 'This invitation link has expired.',
        ], 422);
    }

    $user = User::with('roles')
        ->where('email', $request->email)
        ->first();

    if (! $user) {
        return response()->json([
            'valid' => false,
            'message' => 'User not found.',
        ], 404);
    }

    $role = $user->roles->first()?->name;

    $position = $user->hospitalStaff()
        ->where('is_active', true)
        ->value('position');

    return response()->json([
        'valid' => true,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'role' => $role,
        'position' => $position,
    ]);
}

    public function accept(Request $request)
    {
        $request->validate([
            'token'                 => 'required|string',
            'email'                 => 'required|email',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return response()->json([
                'message' => 'Invalid or expired invitation link.',
            ], 422);
        }

        // Expire after 24 hours
        if (now()->diffInHours($record->created_at) >= 24) {
            return response()->json([
                'message' => 'This invitation link has expired.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Set password and activate account
        $user->update([
            'password'          => Hash::make($request->password),
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // Consume the token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Audit log — resolve the user's hospital_id for tenant isolation
        $hospitalId = $user->hospitalStaff()->where('is_active', true)->value('hospital_id');

        \App\Models\AuditLog::create([
            'user_id'      => $user->id,
            'hospital_id'  => $hospitalId,
            'action'       => 'invitation_accepted',
            'target_table' => 'users',
            'target_id'    => $user->id,
            'details'      => ['email' => $user->email],
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Account activated successfully. You can now log in.',
        ]);
    }
}
