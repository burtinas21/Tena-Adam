<?php

namespace App\Services;

use App\Models\TelehealthAttendance;
use App\Models\TelehealthSession;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TelehealthAttendanceService
{
    /**
     * Record a user joining a telehealth session.
     *
     * @throws ValidationException
     */
    public function joinAttendance(array $data): TelehealthAttendance
    {
        try {
            return DB::transaction(function () use ($data) {
                $session = TelehealthSession::findOrFail($data['session_id']);

                if (! in_array($session->status, ['scheduled', 'active'])) {
                    throw ValidationException::withMessages([
                        'session' => ['Only scheduled or active sessions can be joined.'],
                    ]);
                }

                // Prevent duplicate attendance
                if (TelehealthAttendance::where('session_id', $data['session_id'])
                    ->where('user_id', $data['user_id'])
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'attendance' => ['User already joined this session.'],
                    ]);
                }

                return TelehealthAttendance::create([
                    'session_id' => $data['session_id'],
                    'user_id' => $data['user_id'],
                    'joined_at' => now(),
                    'device_type' => $data['device_type'] ?? null,
                    'ip_address' => $data['ip_address'] ?? request()->ip(),
                ]);
            });
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'session' => ['Telehealth session not found.'],
            ]);
        }
    }

    /**
     * Record a user leaving a telehealth session.
     *
     * @throws ValidationException
     */
    public function leaveAttendance(string $sessionId, string $userId): TelehealthAttendance
    {
        try {
            return DB::transaction(function () use ($sessionId, $userId) {
                $attendance = TelehealthAttendance::where('session_id', $sessionId)
                    ->where('user_id', $userId)
                    ->firstOrFail();

                if ($attendance->left_at) {
                    throw ValidationException::withMessages([
                        'attendance' => ['User has already left this session.'],
                    ]);
                }

                $attendance->update([
                    'left_at' => now(),
                ]);

                return $attendance->fresh();
            });
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'attendance' => ['Attendance record not found.'],
            ]);
        }
    }

    /**
     * List all attendance records for a session.
     */
    public function listAttendance(string $sessionId)
    {
        return TelehealthAttendance::where('session_id', $sessionId)->get();
    }
}
