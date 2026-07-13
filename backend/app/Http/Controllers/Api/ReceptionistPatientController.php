<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ReceptionistPatientController extends Controller
{
    /**
     * Resolve the hospital ID for the currently authenticated user.
     * Works for receptionist and hospital_admin (both have a hospitalStaff record).
     */
    private function myHospitalId(): ?string
    {
        return auth()->user()->hospitalStaff()->value('hospital_id');
    }

    /**
     * Scope a Patient query to only patients registered by staff of the same hospital.
     * A patient is "owned" by a hospital if their registered_by user
     * is a staff member (hospital_admin or receptionist) at that hospital.
     */
    private function scopedPatients(string $hospitalId)
    {
        // Get all user IDs who are staff at this hospital
        $staffUserIds = \App\Models\HospitalStaff::where('hospital_id', $hospitalId)
            ->pluck('user_id');

        return Patient::with(['user'])
            ->whereIn('registered_by', $staffUserIds);
    }

    /**
     * GET /receptionist/patients
     * List patients registered at the receptionist's own hospital only.
     */
    public function index(Request $request): JsonResponse
    {
        $hospitalId = $this->myHospitalId();

        if (!$hospitalId) {
            return response()->json(['data' => []]);
        }

        $patients = $this->scopedPatients($hospitalId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => $this->formatPatient($p));

        return response()->json(['data' => $patients]);
    }

    /**
     * POST /receptionist/patients
     * Register a new patient. No blood_type / allergies / medical_history — doctor fills those.
     */
    public function store(Request $request): JsonResponse
    {
        $hospitalId = $this->myHospitalId();
        if (!$hospitalId) {
            return response()->json(['message' => 'Could not determine your hospital.'], 422);
        }

        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|string|min:8',
            'date_of_birth' => 'required|date',
            'gender'        => 'required|in:male,female,other',
            'address'       => 'nullable|string|max:1000',
            'occupation'    => 'nullable|string|max:100',
            'national_id'   => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'password'   => Hash::make($data['password']),
            'is_active'  => true,
        ]);

        $patientRole = Role::where('name', 'patient')->first();
        $user->roles()->attach($patientRole->id);

        $patient = Patient::create([
            'id'             => $user->id,
            'date_of_birth'  => $data['date_of_birth'],
            'gender'         => $data['gender'],
            'address'        => $data['address'] ?? null,
            'occupation'     => $data['occupation'] ?? null,
            'national_id'    => $data['national_id'] ?? null,
            'patient_status' => 'active',
            'registered_by'  => auth()->id(),   // ties patient to this hospital via staff record
        ]);

        return response()->json([
            'message' => 'Patient registered successfully',
            'data'    => $this->formatPatient($patient->load('user')),
        ], 201);
    }

    /**
     * GET /receptionist/patients/search?q=...
     * Search patients scoped to the receptionist's hospital only.
     */
    public function search(Request $request): JsonResponse
    {
        $q          = $request->query('q', '');
        $hospitalId = $this->myHospitalId();

        if (strlen($q) < 2 || !$hospitalId) {
            return response()->json(['data' => []]);
        }

        $patients = $this->scopedPatients($hospitalId)
            ->whereHas('user', function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                      ->orWhere('last_name',  'like', "%{$q}%")
                      ->orWhere('email',      'like', "%{$q}%")
                      ->orWhere('phone',      'like', "%{$q}%");
            })
            ->limit(15)
            ->get()
            ->map(fn ($p) => $this->formatPatient($p));

        return response()->json(['data' => $patients]);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function formatPatient(Patient $patient): array
    {
        return [
            'id'             => $patient->id,
            'first_name'     => $patient->user?->first_name,
            'last_name'      => $patient->user?->last_name,
            'email'          => $patient->user?->email,
            'phone'          => $patient->user?->phone,
            'gender'         => $patient->gender,
            'date_of_birth'  => $patient->date_of_birth,
            'address'        => $patient->address,
            'occupation'     => $patient->occupation,
            'national_id'    => $patient->national_id,
            'patient_status' => $patient->patient_status,
        ];
    }
}
