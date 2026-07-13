<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vital\StoreVitalRequest;
use App\Http\Requests\Vital\UpdateVitalRequest;
use App\Http\Resources\VitalResource;
use App\Services\VitalsService;
use Illuminate\Http\JsonResponse;

class VitalController extends Controller
{
    public function __construct(
        private VitalsService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Store Vitals
    |--------------------------------------------------------------------------
    */
    public function store(StoreVitalRequest $request): JsonResponse
    {
        $vital = $this->service->storeVitals($request->validated());

        return response()->json([
            'message' => 'Vitals recorded successfully',
            'data'    => new VitalResource($vital),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Vitals
    |--------------------------------------------------------------------------
    */
    public function update(UpdateVitalRequest $request, string $id): JsonResponse
    {
        $vital = $this->service->updateVitals($id, $request->validated());

        return response()->json([
            'message' => 'Vitals updated successfully',
            'data'    => new VitalResource($vital),
        ]);
    }

 
    public function show(string $id): JsonResponse
    {
        $vital = $this->service->findVitals($id);

        return response()->json([
            'data' => new VitalResource($vital),
        ]);
    }
}
