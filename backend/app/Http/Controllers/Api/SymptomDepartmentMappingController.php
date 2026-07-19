<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Symptom\StoreSymptomDepartmentMappingRequest;
use App\Http\Requests\Api\Symptom\UpdateSymptomDepartmentMappingRequest;
use App\Http\Requests\Api\SymptomDepartmentMapping\CreateAppointmentRequest;
use App\Services\SymptomDepartmentMappingService;
use Illuminate\Http\JsonResponse;

class SymptomDepartmentMappingController extends Controller
{
    protected SymptomDepartmentMappingService $service;

    public function __construct(SymptomDepartmentMappingService $service)
    {
        $this->service = $service;
    }

    public function store(StoreSymptomDepartmentMappingRequest $request): JsonResponse
    {
        $this->authorize('create', \App\Models\SymptomDepartmentMapping::class);

        $mapping = $this->service->store($request->validated());

        return response()->json($mapping, 201);
    }

    public function update(UpdateSymptomDepartmentMappingRequest $request, string $id): JsonResponse
    {
        $this->authorize('update', \App\Models\SymptomDepartmentMapping::class);

        $mapping = $this->service->update($id, $request->validated());

        return response()->json($mapping);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->authorize('delete', \App\Models\SymptomDepartmentMapping::class);

        $this->service->delete($id);

        return response()->json(['message' => 'Mapping deleted successfully']);
    }

    public function indexBySymptom(string $symptomId): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\SymptomDepartmentMapping::class);

        $mappings = $this->service->findBySymptom($symptomId);

        return response()->json($mappings);
    }

    public function recommendationsWithAppointment(string $symptomId): JsonResponse
    {
        $recommendations = $this->service->getRecommendationsWithAppointment($symptomId);

        return response()->json($recommendations);
    }

    public function createAppointment(CreateAppointmentRequest $request, string $symptomId): JsonResponse
    {
        $this->authorize('create', \App\Models\Appointment::class);

        $result = $this->service->createAppointmentFromRecommendation(
            $symptomId,
            $request->validated()['patient_id'],
            $request->validated()['scheduled_at']
        );

        return response()->json($result, $result['success'] ? 201 : 400);
    }
}
