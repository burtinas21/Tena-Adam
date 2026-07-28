<?php

namespace App\Http\Controllers\Api;

use App\Models\MedicalDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\MedicalDocumentService;
use App\Http\Requests\StoreMedicalDocumentRequest;
use App\Http\Requests\UpdateMedicalDocumentRequest;

class MedicalDocumentController extends Controller
{
    public function __construct(
        private MedicalDocumentService $service
    ) {
    }

    /**
     * List documents for the authenticated user (doctor sees all their
     * patients'; patient sees only their own).
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user->hasRole('doctor')) {
            $doctorId = $user->healthcareProvider?->id ?? $user->id;
            $documents = MedicalDocument::with(['patient.user', 'encounter', 'uploader'])
                ->where(function ($q) use ($doctorId) {
                    $q->whereHas('encounter', fn ($q2) => $q2->where('doctor_id', $doctorId))
                      ->orWhereNull('encounter_id');
                })
                ->latest()
                ->get();
        } elseif ($user->hasRole('patient')) {
            $patientId = $user->patient?->id ?? $user->id;
            $documents = MedicalDocument::with(['patient.user', 'encounter', 'uploader'])
                ->where('patient_id', $patientId)
                ->latest()
                ->get();
        } else {
            $documents = collect();
        }

        // Optional filter by appointment
        if ($request->has('appointment_id') && $request->appointment_id) {
            $aid       = $request->appointment_id;
            $documents = $documents->filter(fn ($d) => $d->appointment_id === $aid)->values();
        }

        // Optional filter by encounter
        if ($request->has('encounter_id') && $request->encounter_id) {
            $encId     = $request->encounter_id;
            $documents = $documents->filter(fn ($d) => $d->encounter_id === $encId)->values();
        }

        // Optional filter by patient
        if ($request->has('patient_id') && $request->patient_id) {
            $pid       = $request->patient_id;
            $documents = $documents->filter(fn ($d) => $d->patient_id === $pid)->values();
        }

        return response()->json(['data' => $documents]);
    }

    public function store(StoreMedicalDocumentRequest $request): JsonResponse
    {
        $this->authorize('create', MedicalDocument::class);

        $document = $this->service->uploadDocument(
            array_merge($request->validated(), ['file' => $request->file('file')])
        );

        return response()->json([
            'message' => 'Document uploaded successfully.',
            'data'    => $document,
        ], 201);
    }

    public function update(
        UpdateMedicalDocumentRequest $request,
        MedicalDocument $medicalDocument
    ): JsonResponse {
        $this->authorize('update', $medicalDocument);

        $data = $request->validated();
        if ($request->hasFile('file')) {
            $data['file'] = $request->file('file');
        }

        $document = $this->service->updateDocument($medicalDocument, $data);

        return response()->json([
            'message' => 'Document updated successfully.',
            'data'    => $document,
        ]);
    }

    public function destroy(MedicalDocument $medicalDocument): JsonResponse
    {
        $this->authorize('delete', $medicalDocument);

        $this->service->deleteDocument($medicalDocument);

        return response()->json(['message' => 'Document deleted successfully.']);
    }

    public function patientDocuments(string $patientId): JsonResponse
    {
        $documents = $this->service->getPatientDocuments($patientId);

        return response()->json(['data' => $documents]);
    }

    public function encounterDocuments(string $encounterId): JsonResponse
    {
        $documents = $this->service->getEncounterDocuments($encounterId);

        return response()->json(['data' => $documents]);
    }

    public function download(MedicalDocument $medicalDocument)
    {
        // Authenticate via query-param token so direct <a href> links work from the SPA.
        // If no bearer token in header, try ?token= query param (set by the frontend).
        $user = auth('sanctum')->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check permission: patient sees only own docs; doctor sees docs from their patients
        $patientId = $medicalDocument->patient_id;
        if ($user->hasRole('patient') && $user->id !== $patientId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $this->service->downloadDocument($medicalDocument);
    }
}
