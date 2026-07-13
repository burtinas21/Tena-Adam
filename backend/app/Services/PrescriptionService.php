<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\MedicalEncounter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    /*
    |--------------------------------------------------------------------------
    | Create Prescription
    |--------------------------------------------------------------------------
    */
    public function createPrescription(array $data): Prescription
    {
        return DB::transaction(function () use ($data) {
            // Ensure encounter exists
            $encounter = MedicalEncounter::findOrFail($data['encounter_id']);

            // Create prescription
            $prescription = Prescription::create([
                'encounter_id'    => $encounter->id,
                'medication_id'   => $data['medication_id'] ?? null,
                'medication_name' => $data['medication_name'],
                'dosage'          => $data['dosage'],
                'frequency'       => $data['frequency'],
                'route'           => $data['route'] ?? null,
                'duration_days'   => $data['duration_days'] ?? null,
                'quantity'        => $data['quantity'] ?? null,
                'instructions'    => $data['instructions'] ?? null,
                'refills'         => $data['refills'] ?? 0,
                'status'          => $data['status'] ?? 'active',
            ]);

            return $prescription;
        });
    }
    public function updatePrescription(string $id, array $data): Prescription
    {
        return DB::transaction(function () use ($id, $data) {
            $prescription = Prescription::findOrFail($id);

            $prescription->update([
                'medication_id'   => $data['medication_id'] ?? $prescription->medication_id,
                'medication_name' => $data['medication_name'] ?? $prescription->medication_name,
                'dosage'          => $data['dosage'] ?? $prescription->dosage,
                'frequency'       => $data['frequency'] ?? $prescription->frequency,
                'route'           => $data['route'] ?? $prescription->route,
                'duration_days'   => $data['duration_days'] ?? $prescription->duration_days,
                'quantity'        => $data['quantity'] ?? $prescription->quantity,
                'instructions'    => $data['instructions'] ?? $prescription->instructions,
                'refills'         => $data['refills'] ?? $prescription->refills,
                'status'          => $data['status'] ?? $prescription->status,
            ]);

            return $prescription;
        });
    }
    public function findPrescription(string $id): Prescription
    {
        return Prescription::findOrFail($id);
    }


    public function cancelPrescription(string $id): Prescription
    {
        $prescription = Prescription::findOrFail($id);
        $prescription->update(['status' => 'cancelled']);
        return $prescription;
    }
}
