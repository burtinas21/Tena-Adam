<?php

namespace App\Services;

use App\Models\SymptomAnalytic;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class SymptomAnalyticsService
{
    /**
     * Log a new analytics record when a patient selects a symptom.
     */
    public function store(array $data): SymptomAnalytic
    {
        return SymptomAnalytic::create($data);
    }

    /**
     * Get all analytics records (admins/doctors only).
     */
    public function getAll()
    {
        return SymptomAnalytic::with(['symptom', 'department', 'patient'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get top 10 most selected symptoms.
     */
    public function topSymptoms(int $limit = 10)
    {
        return SymptomAnalytic::selectRaw('symptom_id, COUNT(*) as total')
            ->groupBy('symptom_id')
            ->orderByDesc('total')
            ->with('symptom')
            ->limit($limit)
            ->get();
    }
}
