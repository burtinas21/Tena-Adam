<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\Prescription\StorePrescriptionRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Prescription\UpdatePrescriptionRequest;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function __construct(
        private PrescriptionService $service
    ) {}

    /**
     * List prescriptions for the authenticated user (doctor or patient).
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Prescription::with([
            'encounter.patient.user',
            'encounter.doctor.user',
            'encounter.hospital',
            'medication',
        ]);

        if ($user->hasRole('doctor')) {
            $doctorId = $user->healthcareProvider?->id;
            if (!$doctorId) return response()->json(['data' => []]);
            $query->whereHas('encounter', fn ($q) => $q->where('doctor_id', $doctorId));
        } elseif ($user->hasRole('patient')) {
            $patientId = $user->patient?->id ?? $user->id;
            $query->whereHas('encounter', fn ($q) => $q->where('patient_id', $patientId));
        } else {
            return response()->json(['data' => []]);
        }

        // Optional filter by encounter
        if ($request->has('encounter_id')) {
            $query->where('encounter_id', $request->encounter_id);
        }

        $prescriptions = $query->orderByDesc('created_at')->get();

        return response()->json([
            'data' => PrescriptionResource::collection($prescriptions),
        ]);
    }

    /**
     * Store a new prescription.
     */
    public function store(
        StorePrescriptionRequest $request
    ): PrescriptionResource {

        $this->authorize('create', Prescription::class);

        $prescription = $this->service->createPrescription(
            $request->validated()
        );

        return new PrescriptionResource(
            $prescription
        );
    }

    /**
     * Show prescription.
     */
    public function show(
        Prescription $prescription
    ): PrescriptionResource {

        $this->authorize('view', $prescription);

        return new PrescriptionResource(

            $this->service->findPrescription(
                $prescription->id
            )

        );
    }

    /**
     * Update prescription.
     */
    public function update(
        UpdatePrescriptionRequest $request,
        Prescription $prescription
    ): PrescriptionResource {

        $this->authorize('update', $prescription);

        return new PrescriptionResource(

            $this->service->updatePrescription(

                $prescription->id,

                $request->validated()

            )

        );
    }

    /**
     * Complete prescription.
     */
    public function complete(
        Prescription $prescription
    ): PrescriptionResource {

        $this->authorize('complete', $prescription);

        return new PrescriptionResource(

            $this->service->completePrescription(
                $prescription->id
            )

        );
    }

    /**
     * Cancel prescription.
     */
    public function cancel(
        Prescription $prescription
    ): PrescriptionResource {

        $this->authorize('delete', $prescription);

        return new PrescriptionResource(

            $this->service->cancelPrescription(
                $prescription->id
            )

        );
    }
}