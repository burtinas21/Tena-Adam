<?php

namespace App\Services;

use App\Models\SymptomDepartmentMapping;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Models\SymptomAnalytic;
class SymptomDepartmentMappingService
{
    public function store(array $data): SymptomDepartmentMapping
    {
        return SymptomDepartmentMapping::create($data);
    }

    public function update(string $id, array $data): SymptomDepartmentMapping
    {
        try {
            $mapping = SymptomDepartmentMapping::findOrFail($id);
            $mapping->update($data);

            return $mapping->fresh();
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'mapping' => ['Mapping not found.'],
            ]);
        }
    }

    public function delete(string $id): void
    {
        try {
            $mapping = SymptomDepartmentMapping::findOrFail($id);
            $mapping->delete();
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'mapping' => ['Mapping not found.'],
            ]);
        }
    }

    public function findBySymptom(string $symptomId)
    {
        return SymptomDepartmentMapping::where('symptom_id', $symptomId)
            ->with('department')
            ->orderByDesc('relevance_score')
            ->get();
    }
     public function getRecommendationsWithAppointment(string $symptomId): array
{
    $mappings = SymptomDepartmentMapping::where('symptom_id', $symptomId)
        ->with('department')
        ->orderByDesc('relevance_score')
        ->get();

    if ($mappings->isEmpty()) {
        return [
            'primary' => null,
            'alternatives' => [],
            'appointment_suggestion' => null,
        ];
    }

    $primary = $mappings->firstWhere('is_primary', true) ?? $mappings->first();

    $alternatives = $mappings->filter(fn($m) => $m->id !== $primary->id)->values();

    return [
        'primary' => [
            'department' => $primary->department->name,
            'relevance_score' => $primary->relevance_score,
            'evidence_level' => $primary->evidence_level,
        ],
        'alternatives' => $alternatives->map(fn($m) => [
            'department' => $m->department->name,
            'relevance_score' => $m->relevance_score,
            'evidence_level' => $m->evidence_level,
        ]),
        'appointment_suggestion' => [
            'department_id' => $primary->department->id,
            'department_name' => $primary->department->name,
            'message' => "Would you like to book an appointment with {$primary->department->name}?"
        ]
    ];
}
public function createAppointmentFromRecommendation(string $symptomId, string $patientId, string $scheduledAt): array
{
    $recommendations = $this->getRecommendationsWithAppointment($symptomId);

    if (!$recommendations['primary']) {
        return [
            'success' => false,
            'message' => 'No department recommendation available for this symptom.'
        ];
    }

    // Create appointment in the recommended department with chosen date/time
    $appointment = \App\Models\Appointment::create([
        'patient_id' => $patientId,
        'department_id' => $recommendations['appointment_suggestion']['department_id'],
        'status' => 'pending',
        'scheduled_at' => $scheduledAt, // patient-selected date/time
        'notes' => "Auto-created from symptom guidance for {$recommendations['primary']['department']}"
    ]);

    return [
        'success' => true,
        'appointment' => $appointment,
        'message' => "Appointment created with {$recommendations['primary']['department']} on {$scheduledAt}."
    ];
}

  
 
}