<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVitalRequest;
use App\Http\Requests\UpdateVitalRequest;
use App\Http\Resources\VitalResource;
use App\Models\Vital;
use App\Services\VitalService;
use Illuminate\Http\JsonResponse;

class VitalController extends Controller
{
    public function __construct(
        private VitalService $service
    ) {}

    /**
     * Store vital signs.
     */
    public function store(
        StoreVitalRequest $request
    ): VitalResource {

        $this->authorize('create', Vital::class);

        $vital = $this->service->createVital(
            $request->validated()
        );

        return new VitalResource($vital);
    }

    /**
     * Display a vital record.
     */
    public function show(
        Vital $vital
    ): VitalResource {

        $this->authorize('view', $vital);

        return new VitalResource(

            $this->service->findVital(
                $vital->id
            )

        );
    }

    /**
     * Update vital signs.
     */
    public function update(
        UpdateVitalRequest $request,
        Vital $vital
    ): VitalResource {

        $this->authorize('update', $vital);

        return new VitalResource(

            $this->service->updateVital(
                $vital->id,
                $request->validated()
            )

        );
    }

    /**
     * Delete vital signs.
     */
    public function destroy(
        Vital $vital
    ): JsonResponse {

        $this->authorize('delete', $vital);

        $this->service->deleteVital(
            $vital->id
        );

        return response()->json([
            'message' => 'Vital record deleted successfully.'
        ]);
    }
}