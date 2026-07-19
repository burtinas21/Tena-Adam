<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SymptomAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SymptomAnalyticsController extends Controller
{
    protected SymptomAnalyticsService $service;

    public function __construct(SymptomAnalyticsService $service)
    {
        $this->service = $service;
    }

    /**
     * Patients log analytics when selecting symptoms.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', \App\Models\SymptomAnalytic::class);

        $data = $request->validate([
            'symptom_id' => 'required|uuid|exists:symptoms,id',
            'recommended_department_id' => 'required|uuid|exists:departments,id',
            'selected_by_patient' => 'boolean',
            'patient_id' => 'nullable|uuid|exists:patients,id',
            'session_id' => 'nullable|uuid',
        ]);

        $analytics = $this->service->store($data);

        return response()->json($analytics, 201);
    }

    /**
     * Admins/Doctors view all analytics.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\SymptomAnalytic::class);

        $analytics = $this->service->getAll();

        return response()->json($analytics);
    }

    /**
     * Admins/Doctors view top symptoms.
     */
    public function topSymptoms(): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\SymptomAnalytic::class);

        $top = $this->service->topSymptoms();

        return response()->json($top);
    }
}
