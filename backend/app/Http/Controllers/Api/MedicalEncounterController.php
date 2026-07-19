<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Encounter\StoreMedicalEncounterRequest;
use App\Http\Requests\Api\Encounter\UpdateMedicalEncounterRequest;
use App\Http\Resources\MedicalEncounterResource;
use App\Models\MedicalEncounter;
use App\Services\MedicalEncounterService;
use Illuminate\Http\JsonResponse;

class MedicalEncounterController extends Controller
{
    public function __construct(
        private MedicalEncounterService $service
    ) {}

    /**
     * List encounters for the authenticated user.
     * For doctors: only returns IN_PROGRESS encounters (patient currently in consultation).
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();

        if ($user->hasRole('doctor')) {
            $doctorId = $user->healthcareProvider?->id;
            if (!$doctorId) return response()->json(['data' => []]);
            $encounters = MedicalEncounter::with([
                'patient.user', 'doctor.user', 'hospital', 'appointment', 'vital',
            ])
            ->where('doctor_id', $doctorId)
            ->where('status', 'in_progress')   // only active consultations in the work list
            ->orderByDesc('encounter_date')
            ->get();
        } elseif ($user->hasRole('patient')) {
            $encounters = MedicalEncounter::with([
                'patient.user', 'doctor.user', 'hospital', 'appointment', 'vital',
            ])
            ->where('patient_id', $user->patient?->id ?? $user->id)
            ->orderByDesc('encounter_date')
            ->get();
        } else {
            return response()->json(['data' => []]);
        }

        return response()->json([
            'data' => MedicalEncounterResource::collection($encounters),
        ]);
    }

    /**
     * Start a consultation and create a medical encounter.
     */
    public function store(
        StoreMedicalEncounterRequest $request
    ): JsonResponse {

        $this->authorize('create', MedicalEncounter::class);

        $encounter = $this->service->createEncounter(
            $request->validated()
        );

        return response()->json([
            'message' => 'Medical encounter started successfully.',
            'data' => new MedicalEncounterResource($encounter),
        ], 201);
    }

    /**
     * View a medical encounter.
     */
    public function show(
        string $encounterId
    ): JsonResponse {

        $encounter = $this->service->findEncounter(
            $encounterId
        );

        $this->authorize('view', $encounter);

        return response()->json([
            'data' => new MedicalEncounterResource($encounter),
        ]);
    }

    /**
     * Update the medical encounter.
     */
    public function update(
        UpdateMedicalEncounterRequest $request,
        string $encounterId
    ): JsonResponse {

        $encounter = $this->service->findEncounter(
            $encounterId
        );

        $this->authorize('update', $encounter);

        $encounter = $this->service->updateEncounter(
            $encounterId,
            $request->validated()
        );

        return response()->json([
            'message' => 'Medical encounter updated successfully.',
            'data' => new MedicalEncounterResource($encounter),
        ]);
    }
    /**
     * Complete a medical encounter.
     */
public function complete(
    MedicalEncounter $medicalEncounter
): JsonResponse
{
    $this->authorize(
        'complete',
        $medicalEncounter
    );

    $encounter = $this->service->completeEncounter(
        $medicalEncounter->id
    );

    return response()->json([
        'message' => 'Consultation completed successfully.',
        'data' => new MedicalEncounterResource(
            $encounter
        ),
    ]);
}

    /**
     * Get completed encounters for a specific patient (doctor viewing patient history timeline).
     * Returns only COMPLETED encounters — the medical history of past visits.
     * Any authenticated doctor can view a patient's completed history.
     */
    public function patientHistory(string $patientId): JsonResponse
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['doctor', 'hospital_admin', 'platform_admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $encounters = MedicalEncounter::with([
            'patient.user', 'doctor.user', 'hospital', 'appointment', 'vital',
        ])
        ->where('patient_id', $patientId)
        ->where('status', 'completed')     // only past completed visits for the history
        ->orderByDesc('encounter_date')
        ->get();

        return response()->json([
            'data' => MedicalEncounterResource::collection($encounters),
        ]);
    }

    /**
     * Doctor updates patient's persistent medical profile fields
     * (blood_type, allergies, medical_history) during consultation.
     */
    public function updatePatientMedical(
        \Illuminate\Http\Request $request,
        string $encounterId
    ): JsonResponse {
        $encounter = $this->service->findEncounter($encounterId);

        $this->authorize('update', $encounter);

        $data = $request->validate([
            'blood_type'      => 'nullable|string|max:10',
            'allergies'       => 'nullable|string|max:1000',
            'medical_history' => 'nullable|string|max:5000',
        ]);

        $patient = \App\Models\Patient::findOrFail($encounter->patient_id);
        $patient->update(array_filter($data, fn($v) => $v !== null));

        return response()->json([
            'message' => 'Patient medical profile updated.',
            'data'    => [
                'blood_type'      => $patient->blood_type,
                'allergies'       => $patient->allergies,
                'medical_history' => $patient->medical_history,
            ],
        ]);
    }
}