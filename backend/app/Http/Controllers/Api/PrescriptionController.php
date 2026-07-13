<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prescription\StorePrescriptionRequest;
use App\Http\Requests\Prescription\UpdatePrescriptionRequest;
use App\Http\Resources\PrescriptionResource;
use App\Services\PrescriptionService;
use Illuminate\Http\JsonResponse;

class PrescriptionController extends Controller
{
    public function __construct(
        private PrescriptionService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Store Prescription
    |--------------------------------------------------------------------------
    */
    public function store(StorePrescriptionRequest $request): JsonResponse
    {
        $prescription = $this->service->createPrescription(
            $request->validated()
        );

        return response()->json([
            'message' => 'Prescription created successfully',
            'data'    => new PrescriptionResource($prescription),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Prescription
    |--------------------------------------------------------------------------
    */
    public function update(UpdatePrescriptionRequest $request, string $id): JsonResponse
    {
        $prescription = $this->service->updatePrescription(
            $id,
            $request->validated()
        );

        return response()->json([
            'message' => 'Prescription updated successfully',
            'data'    => new PrescriptionResource($prescription),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show Prescription
    |--------------------------------------------------------------------------
    */
    public function show(string $id): JsonResponse
    {
        $prescription = $this->service->findPrescription($id);

        return response()->json([
            'data' => new PrescriptionResource($prescription),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Prescription
    |--------------------------------------------------------------------------
    */
    public function cancel(string $id): JsonResponse
    {
        $prescription = $this->service->cancelPrescription($id);

        return response()->json([
            'message' => 'Prescription cancelled successfully',
            'data'    => new PrescriptionResource($prescription),
        ]);
    }
}
