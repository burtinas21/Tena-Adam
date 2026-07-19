<?php

namespace App\Services;

use App\Models\MedicalEncounter;
use App\Models\Vital;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VitalService
{
    /**
     * Create patient vital signs.
     *
     * @throws ValidationException
     */
    public function createVital(array $data): Vital
    {
        try {
            return DB::transaction(function () use ($data) {

                // Load the encounter
                $encounter = MedicalEncounter::findOrFail($data['encounter_id']);

                // Verify doctor ownership
                $doctorId = auth()->user()->healthcareProvider?->id ?? auth()->id();
                if ($encounter->doctor_id !== $doctorId) {
                    throw ValidationException::withMessages([
                        'doctor' => ['You are not assigned to this encounter.'],
                    ]);
                }

                // Prevent duplicate vitals per encounter
                if (Vital::where('encounter_id', $encounter->id)->exists()) {
                    throw ValidationException::withMessages([
                        'encounter' => ['Vital signs already exist for this encounter.'],
                    ]);
                }

                $bmi = $this->calculateBMI(
                    $data['weight'] ?? null,
                    $data['height'] ?? null
                );

                $vital = Vital::create([
                    'encounter_id'             => $encounter->id,
                    'patient_id'               => $encounter->patient_id,
                    'blood_pressure_systolic'  => $data['blood_pressure_systolic'] ?? null,
                    'blood_pressure_diastolic' => $data['blood_pressure_diastolic'] ?? null,
                    'pulse_rate'               => $data['pulse_rate'] ?? null,
                    'respiratory_rate'         => $data['respiratory_rate'] ?? null,
                    'temperature'              => $data['temperature'] ?? null,
                    'weight'                   => $data['weight'] ?? null,
                    'height'                   => $data['height'] ?? null,
                    'bmi'                      => $bmi,
                    'blood_oxygen'             => $data['blood_oxygen'] ?? null,
                    'measured_at'              => $data['measured_at'] ?? now(),
                ]);

                return $this->loadRelations($vital);
            });
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'encounter' => ['Medical encounter not found.'],
            ]);
        }
    }

    /**
     * Find a vital record.
     */
    public function findVital(string $vitalId): Vital
    {
        try {
            return $this->loadRelations(Vital::findOrFail($vitalId));
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'vital' => ['Vital record not found.'],
            ]);
        }
    }

    /**
     * Update vital signs.
     *
     * @throws ValidationException
     */
    public function updateVital(string $vitalId, array $data): Vital
    {
        try {
            return DB::transaction(function () use ($vitalId, $data) {
                $vital = $this->findVitalOrFail($vitalId);

                // Verify doctor ownership
                $doctorId = auth()->user()->healthcareProvider?->id ?? auth()->id();
                if ($vital->encounter->doctor_id !== $doctorId) {
                    throw ValidationException::withMessages([
                        'doctor' => ['You are not assigned to this encounter.'],
                    ]);
                }

                $vital->fill([
                    'blood_pressure_systolic'  => $data['blood_pressure_systolic']  ?? $vital->blood_pressure_systolic,
                    'blood_pressure_diastolic' => $data['blood_pressure_diastolic'] ?? $vital->blood_pressure_diastolic,
                    'pulse_rate'               => $data['pulse_rate']               ?? $vital->pulse_rate,
                    'respiratory_rate'         => $data['respiratory_rate']         ?? $vital->respiratory_rate,
                    'temperature'              => $data['temperature']              ?? $vital->temperature,
                    'weight'                   => $data['weight']                   ?? $vital->weight,
                    'height'                   => $data['height']                   ?? $vital->height,
                    'blood_oxygen'             => $data['blood_oxygen']             ?? $vital->blood_oxygen,
                    'measured_at'              => $data['measured_at']              ?? $vital->measured_at,
                ]);

                $vital->bmi = $this->calculateBMI($vital->weight, $vital->height);
                $vital->save();

                return $this->loadRelations($vital->fresh());
            });
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'vital' => ['Vital record not found.'],
            ]);
        }
    }

    /**
     * Delete a vital record.
     */
    public function deleteVital(string $vitalId): void
    {
        try {
            DB::transaction(function () use ($vitalId) {
                $vital    = $this->findVitalOrFail($vitalId);
                $doctorId = auth()->user()->healthcareProvider?->id ?? auth()->id();

                if ($vital->encounter->doctor_id !== $doctorId) {
                    throw ValidationException::withMessages([
                        'doctor' => ['You are not assigned to this encounter.'],
                    ]);
                }

                $vital->delete();
            });
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'vital' => ['Vital record not found.'],
            ]);
        }
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function findVitalOrFail(string $vitalId): Vital
    {
        return Vital::with(['encounter'])->findOrFail($vitalId);
    }

    private function loadRelations(Vital $vital): Vital
    {
        return $vital->load([
            'patient.user',
            'encounter.doctor.user',
            'encounter.hospital',
        ]);
    }

    private function calculateBMI(?float $weight, ?float $height): ?float
    {
        if (empty($weight) || empty($height)) {
            return null;
        }
        $heightM = $height / 100;
        return round($weight / ($heightM * $heightM), 2);
    }
}
