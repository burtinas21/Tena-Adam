<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthcareProvider;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\UpdateHealthcareProviderRequest;
use App\Services\HealthcareProviderService;
use App\Http\Resources\HealthcareProviderResource;

class HealthcareProviderController extends Controller
{
    public function __construct(
        private HealthcareProviderService $service
    ) {}

    /**
     * List healthcare providers.
     * - platform_admin  → all
     * - hospital_admin  → their hospitals only
     * - receptionist    → their hospital only
     * - doctor          → colleagues in their hospital
     * - patient / guest → all (for browsing / booking)
     */
    public function index()
    {
        $user = auth()->user();

        $query = HealthcareProvider::with([
            'user',
            'department',
            'hospital',
            'specializations',
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews');

        if ($user->hasRole('platform_admin')) {
            // sees all — no extra scope
        } elseif ($user->hasRole('hospital_admin')) {
            $hospitalIds = $user->hospitals()->pluck('hospitals.id');
            $query->whereIn('hospital_id', $hospitalIds);
        } elseif ($user->hasRole('receptionist')) {
            $hospitalId = $user->hospitalStaff()->value('hospital_id');
            if (!$hospitalId) {
                return HealthcareProviderResource::collection(collect([]));
            }
            $query->where('hospital_id', $hospitalId);
        } elseif ($user->hasRole('doctor')) {
            // A doctor sees colleagues at their own hospital
            $hospitalId = $user->hospitalStaff()->value('hospital_id');
            if ($hospitalId) {
                $query->where('hospital_id', $hospitalId);
            }
        }
        // patients / unauthenticated see all

        return HealthcareProviderResource::collection($query->get());
    }

    /**
     * Create a new healthcare provider (doctor profile).
     */
    public function store(CreateDoctorRequest $request)
    {
        $this->authorize('create', HealthcareProvider::class);

        $provider = $this->service->create($request->validated());

        return response()->json([
            'message' => 'Doctor profile created',
            'data'    => new HealthcareProviderResource($provider),
        ], 201);
    }

    /**
     * Show a single healthcare provider with full detail.
     */
    public function show(HealthcareProvider $provider)
    {
        $this->authorize('view', $provider);

        $provider->load([
            'user',
            'department',
            'hospital',
            'specializations',
        ]);

        $provider->loadCount('reviews');
        $provider->loadAvg('reviews', 'rating');

        return new HealthcareProviderResource($provider);
    }

    /**
     * Update a healthcare provider.
     */
    public function update(
        UpdateHealthcareProviderRequest $request,
        HealthcareProvider $provider
    ) {
        $this->authorize('update', $provider);

        $provider = $this->service->update($provider, $request->validated());

        return response()->json([
            'message' => 'Doctor updated successfully',
            'data'    => new HealthcareProviderResource($provider),
        ]);
    }

    /**
     * Delete a healthcare provider.
     */
    public function destroy(HealthcareProvider $provider)
    {
        $this->authorize('delete', $provider);

        $this->service->delete($provider);

        return response()->json(['message' => 'Deleted']);
    }
}
