<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Symptom\StoreSymptomRequest;
use App\Http\Requests\Api\Symptom\UpdateSymptomRequest;
use App\Services\SymptomService;
use App\Models\Symptom;
use Illuminate\Http\JsonResponse;

class SymptomController extends Controller
{
    protected SymptomService $service;

    public function __construct(SymptomService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Symptom::class);

        $symptoms = Symptom::orderBy('category')->orderBy('name')->get();

        return response()->json($symptoms);
    }

    public function show(string $id): JsonResponse
    {
        $symptom = $this->service->find($id);
        $this->authorize('view', $symptom);

        return response()->json($symptom->load('departmentMappings.department'));
    }

    public function store(StoreSymptomRequest $request): JsonResponse
    {
        $this->authorize('create', Symptom::class);

        $symptom = $this->service->store($request->validated());

        return response()->json($symptom, 201);
    }

    public function update(UpdateSymptomRequest $request, string $id): JsonResponse
    {
        $symptom = $this->service->find($id);
        $this->authorize('update', $symptom);

        $symptom = $this->service->update($id, $request->validated());

        return response()->json($symptom);
    }

    public function destroy(string $id): JsonResponse
    {
        $symptom = $this->service->find($id);
        $this->authorize('delete', $symptom);

        $this->service->delete($id);

        return response()->json(['message' => 'Symptom deleted successfully']);
    }
}
