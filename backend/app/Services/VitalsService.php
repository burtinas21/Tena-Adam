<?php

namespace App\Services;

use App\Models\Vital;
use App\Models\MedicalEncounter;
use App\Models\Patient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class VitalsService
{
    /*
    |--------------------------------------------------------------------------
    | Store Vitals
    |--------------------------------------------------------------------------
    | Doctors record vitals during an encounter.
    */
    public function storeVitals(array $data): Vital
    {
        return DB::transaction(function () use ($data) {
            // Ensure encounter and patient exist
            $encounter = MedicalEncounter::findOrFail($data['encounter_id']);
            $patient   = Patient::findOrFail($data['patient_id']);

            // Auto-calculate BMI if weight & height provided
            $bmi = null;
            if (!empty($data['weight']) && !empty($data['height']) && $data['height'] > 0) {
                $bmi = round($data['weight'] / ($data['height'] * $data['height']), 2);
            }

            $vital = Vital::create([
                'encounter_id'            => $encounter->id,
                'patient_id'              => $patient->id,
                'blood_pressure_systolic' => $data['blood_pressure_systolic'] ?? null,
                'blood_pressure_diastolic'=> $data['blood_pressure_diastolic'] ?? null,
                'pulse_rate'              => $data['pulse_rate'] ?? null,
                'respiratory_rate'        => $data['respiratory_rate'] ?? null,
                'temperature'             => $data['temperature'] ?? null,
                'weight'                  => $data['weight'] ?? null,
                'height'                  => $data['height'] ?? null,
                'bmi'                     => $bmi,
                'blood_oxygen'            => $data['blood_oxygen'] ?? null,
                'measured_at'             => $data['measured_at'] ?? now(),
            ]);

            return $vital;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Update Vitals
    |--------------------------------------------------------------------------
    | Doctors can update vitals if corrections are needed.
    */
    public function updateVitals(string $id, array $data): Vital
    {
        return DB::transaction(function () use ($id, $data) {
            $vital = Vital::findOrFail($id);

            // Recalculate BMI if weight/height updated
            $bmi = $vital->bmi;
            if (!empty($data['weight']) && !empty($data['height']) && $data['height'] > 0) {
                $bmi = round($data['weight'] / ($data['height'] * $data['height']), 2);
            }

            $vital->update([
                'blood_pressure_systolic' => $data['blood_pressure_systolic'] ?? $vital->blood_pressure_systolic,
                'blood_pressure_diastolic'=> $data['blood_pressure_diastolic'] ?? $vital->blood_pressure_diastolic,
                'pulse_rate'              => $data['pulse_rate'] ?? $vital->pulse_rate,
                'respiratory_rate'        => $data['respiratory_rate'] ?? $vital->respiratory_rate,
                'temperature'             => $data['temperature'] ?? $vital->temperature,
                'weight'                  => $data['weight'] ?? $vital->weight,
                'height'                  => $data['height'] ?? $vital->height,
                'bmi'                     => $bmi,
                'blood_oxygen'            => $data['blood_oxygen'] ?? $vital->blood_oxygen,
                'measured_at'             => $data['measured_at'] ?? $vital->measured_at,
            ]);

            return $vital;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Find Vitals
    |--------------------------------------------------------------------------
    */
    public function findVitals(string $id): Vital
    {
        return Vital::findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Recommendations / Improvements
    |--------------------------------------------------------------------------
    | In a real SaaS hospital system, you can extend this service with:
    | - Alerts: flag abnormal vitals (e.g., BP > 180/120, O2 < 90%)
    | - Audit logs: track who recorded vitals and when
    | - Analytics: average vitals per patient, trends over time
    | - Integration: push vitals to monitoring dashboards or EHR analytics
    */
}
