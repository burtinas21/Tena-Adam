<?php

namespace App\Services;

use App\Models\Medication;
use App\Models\MedicalEncounter;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\NotificationService;

class PrescriptionService
{   private NotificationService $notificationService;

public function __construct(
    NotificationService $notificationService
) {
    $this->notificationService = $notificationService;
}
    /**
     * Create a prescription.
     *
     * @throws ValidationException
     */
    public function createPrescription(array $data): Prescription
    {
        return DB::transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Find Encounter
            |--------------------------------------------------------------------------
            */

            $encounter = MedicalEncounter::with([
                'doctor',
                'patient',
                'hospital',
                'prescriptions',
            ])->findOrFail(
                $data['encounter_id']
            );

            /*
            |--------------------------------------------------------------------------
            | Encounter must be in progress
            |--------------------------------------------------------------------------
            */

            if ($encounter->status !== 'in_progress') {

                throw ValidationException::withMessages([
                    'encounter' => [
                        'Prescription can only be created during an active consultation.'
                    ]
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | Verify Doctor Ownership
            |--------------------------------------------------------------------------
            */

            $authDoctorId = auth()->user()->healthcareProvider?->id ?? auth()->id();
            if ($encounter->doctor_id !== $authDoctorId) {

                throw ValidationException::withMessages([
                    'doctor' => [
                        'You are not assigned to this medical encounter.'
                    ]
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | Validate Medication
            |--------------------------------------------------------------------------
            */

            $this->validateMedication($data);

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Prescription
            |--------------------------------------------------------------------------
            */

            $this->ensurePrescriptionDoesNotExist(
                $encounter->id,
                $data
            );

            /*
            |--------------------------------------------------------------------------
            | Create Prescription
            |--------------------------------------------------------------------------
            */

            $prescription = Prescription::create([

                'encounter_id' => $encounter->id,

                'medication_id' =>
                    $data['medication_id'] ?? null,

                'medication_name' =>
                    $data['medication_name'],

                'dosage' =>
                    $data['dosage'],

                'frequency' =>
                    $data['frequency'],

                'route' =>
                    $data['route'] ?? null,

                'duration_days' =>
                    $data['duration_days'] ?? null,

                'quantity' =>
                    $data['quantity'] ?? null,

                'instructions' =>
                    $data['instructions'] ?? null,

                'refills' =>
                    $data['refills'] ?? 0,

                'status' => 'active',

            ]);
            $prescription = $this->loadRelations($prescription);

            $this->notificationService->sendPrescriptionNotification(
                $prescription,
                'Prescription Created',
                'A new prescription has been created for your consultation.',
                true
            );

            return $prescription;

        });
    }

    /**
     * Find prescription.
     *
     * @throws ValidationException
     */
    public function findPrescription(
        string $prescriptionId
    ): Prescription {

        try {

            return $this->loadRelations(

                Prescription::findOrFail(
                    $prescriptionId
                )

            );

        } catch (ModelNotFoundException $e) {

            throw ValidationException::withMessages([
                'prescription' => [
                    'Prescription not found.'
                ]
            ]);

        }

    }

    /**
     * Validate medication.
     */
    private function validateMedication(array $data): void
    {
        /*
        |--------------------------------------------------------------------------
        | Medication selected from catalog
        |--------------------------------------------------------------------------
        */

        if (!empty($data['medication_id'])) {

            Medication::findOrFail(
                $data['medication_id']
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Medication name required
        |--------------------------------------------------------------------------
        */

        if (empty(trim($data['medication_name']))) {

            throw ValidationException::withMessages([
                'medication_name' => [
                    'Medication name is required.'
                ]
            ]);

        }
    }
   
    /**
 * Update an existing prescription.
 *
 * @throws ValidationException
 */
public function updatePrescription(
    string $prescriptionId,
    array $data
): Prescription {

    try {

        return DB::transaction(function () use (
            $prescriptionId,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Find Prescription
            |--------------------------------------------------------------------------
            */

            $prescription = $this->findPrescriptionOrFail(
                $prescriptionId
            );

            /*
            |--------------------------------------------------------------------------
            | Ensure Prescription Can Be Updated
            |--------------------------------------------------------------------------
            */

            $this->ensureEditable(
                $prescription
            );

            /*
            |--------------------------------------------------------------------------
            | Validate Medication
            |--------------------------------------------------------------------------
            */

            $this->validateMedication($data);

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Medication
            |--------------------------------------------------------------------------
            */

            $this->ensurePrescriptionDoesNotExist(

                $prescription->encounter_id,

                $data,

                $prescription->id

            );

            /*
            |--------------------------------------------------------------------------
            | Update Prescription
            |--------------------------------------------------------------------------
            */

            $prescription->fill([

                'medication_id' =>
                    $data['medication_id']
                        ?? $prescription->medication_id,

                'medication_name' =>
                    $data['medication_name']
                        ?? $prescription->medication_name,

                'dosage' =>
                    $data['dosage']
                        ?? $prescription->dosage,

                'frequency' =>
                    $data['frequency']
                        ?? $prescription->frequency,

                'route' =>
                    $data['route']
                        ?? $prescription->route,

                'duration_days' =>
                    $data['duration_days']
                        ?? $prescription->duration_days,

                'quantity' =>
                    $data['quantity']
                        ?? $prescription->quantity,

                'instructions' =>
                    $data['instructions']
                        ?? $prescription->instructions,

                'refills' =>
                    $data['refills']
                        ?? $prescription->refills,

            ]);

            $prescription->save();

            return $this->loadRelations(
                $prescription->fresh()
            );

        });

    } catch (ModelNotFoundException $e) {

        throw ValidationException::withMessages([
            'prescription' => [
                'Prescription not found.'
            ]
        ]);

    }

}
/**
 * Complete a prescription.
 *
 * @throws ValidationException
 */
public function completePrescription(
    string $prescriptionId
): Prescription {

    try {

        return DB::transaction(function () use ($prescriptionId) {

            /*
            |--------------------------------------------------------------------------
            | Find Prescription
            |--------------------------------------------------------------------------
            */

            $prescription = $this->findPrescriptionOrFail(
                $prescriptionId
            );

            /*
            |--------------------------------------------------------------------------
            | Ensure Editable
            |--------------------------------------------------------------------------
            */

            $this->ensureEditable(
                $prescription
            );

            /*
            |--------------------------------------------------------------------------
            | Complete Prescription
            |--------------------------------------------------------------------------
            */

            $prescription->update([
                'status' => 'completed',
            ]);

         $prescription = $this->loadRelations(
    $prescription->fresh()
);

$this->notificationService->sendPrescriptionNotification(
    $prescription,
    'Prescription Completed',
    'Your prescription is ready.',
    true
);

return $prescription;

        });

    } catch (ModelNotFoundException $e) {

        throw ValidationException::withMessages([
            'prescription' => [
                'Prescription not found.'
            ]
        ]);

    }
}
/**
 * Cancel a prescription.
 *
 * @throws ValidationException
 */
public function cancelPrescription(
    string $prescriptionId
): Prescription {

    try {

        return DB::transaction(function () use ($prescriptionId) {

            /*
            |--------------------------------------------------------------------------
            | Find Prescription
            |--------------------------------------------------------------------------
            */

            $prescription = $this->findPrescriptionOrFail(
                $prescriptionId
            );

            /*
            |--------------------------------------------------------------------------
            | Already Cancelled
            |--------------------------------------------------------------------------
            */

            if ($prescription->status === 'cancelled') {

                throw ValidationException::withMessages([
                    'prescription' => [
                        'Prescription has already been cancelled.'
                    ]
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | Completed Prescription Cannot Be Cancelled
            |--------------------------------------------------------------------------
            */

            if ($prescription->status === 'completed') {

                throw ValidationException::withMessages([
                    'prescription' => [
                        'Completed prescriptions cannot be cancelled.'
                    ]
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | Cancel Prescription
            |--------------------------------------------------------------------------
            */

            $prescription->update([
                'status' => 'cancelled',
            ]);

          $prescription = $this->loadRelations(
    $prescription->fresh()
);

$this->notificationService->sendPrescriptionNotification(
    $prescription,
    'Prescription Cancelled',
    'Your prescription has been cancelled.',
    true
);

return $prescription;

        });

    } catch (ModelNotFoundException $e) {

        throw ValidationException::withMessages([
            'prescription' => [
                'Prescription not found.'
            ]
        ]);

    }
}

/**
 * Prevent duplicate prescriptions.
 *
 * @throws ValidationException
 */
private function ensurePrescriptionDoesNotExist(
    string $encounterId,
    array $data,
    ?string $ignoreId = null
): void {

    $query = Prescription::where(
        'encounter_id',
        $encounterId
    );

    if (!empty($data['medication_id'])) {

        $query->where(
            'medication_id',
            $data['medication_id']
        );

    } else {

        $query->where(
            'medication_name',
            $data['medication_name']
        );

    }

    if ($ignoreId) {

        $query->where(
            'id',
            '!=',
            $ignoreId
        );

    }

    if ($query->exists()) {

        throw ValidationException::withMessages([
            'prescription' => [
                'This medication has already been prescribed during this encounter.'
            ]
        ]);

    }
}
/**
 * Find prescription by ID.
 *
 * @throws ModelNotFoundException
 */
private function findPrescriptionOrFail(
    string $prescriptionId
): Prescription {

    return Prescription::findOrFail(
        $prescriptionId
    );

}

/**
 * Ensure prescription is editable.
 *
 * @throws ValidationException
 */
private function ensureEditable(
    Prescription $prescription
): void {

    if (
        in_array(
            $prescription->status,
            ['completed', 'cancelled']
        )
    ) {

        throw ValidationException::withMessages([
            'prescription' => [
                'Completed or cancelled prescriptions cannot be modified.'
            ]
        ]);

    }

}

/**
 * Load required relationships.
 */
private function loadRelations(
    Prescription $prescription
): Prescription {

    return $prescription->load([

        'encounter',

        'encounter.patient.user',

        'encounter.doctor.user',

        'encounter.hospital',

        'medication',

    ]);

}
}