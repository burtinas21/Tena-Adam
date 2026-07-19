<?php

namespace App\Services;

use App\Models\Medication;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MedicationService
{
        public function createMedication(array $data): Medication
    {
        return DB::transaction(function () use ($data) {

            $this->ensureMedicationDoesNotExist(
                $data['name'],
                $data['strength'] ?? null,
                $data['dosage_form']
            );

            $medication = Medication::create([

                'name' => $data['name'],

                'generic_name' => $data['generic_name'] ?? null,

                'manufacturer' => $data['manufacturer'] ?? null,

                'dosage_form' => $data['dosage_form'],

                'strength' => $data['strength'] ?? null,

                'category' => $data['category'] ?? null,

                'requires_prescription' =>
                    $data['requires_prescription'] ?? true,

                'side_effects' =>
                    $data['side_effects'] ?? null,

                'interactions' =>
                    $data['interactions'] ?? null,

            ]);

            return $medication->fresh();
        });
    }
    public function findMedication(string $medicationId): Medication
    {
        try {

            return Medication::findOrFail(
                $medicationId
            );

        } catch (ModelNotFoundException $e) {

            throw ValidationException::withMessages([
                'medication' => [
                    'Medication not found.'
                ]
            ]);

        }
    }

    /**
     * Update medication.
     *
     * @throws ValidationException
     */
    public function updateMedication(
        string $medicationId,
        array $data
    ): Medication {

        return DB::transaction(function () use (
            $medicationId,
            $data
        ) {

            $medication = $this->findMedication(
                $medicationId
            );

            if (
                isset($data['name']) ||
                isset($data['strength']) ||
                isset($data['dosage_form'])
            ) {

                $this->ensureMedicationDoesNotExist(

                    $data['name']
                        ?? $medication->name,

                    $data['strength']
                        ?? $medication->strength,

                    $data['dosage_form']
                        ?? $medication->dosage_form,

                    $medication->id

                );

            }

            $medication->fill([

                'name' =>
                    $data['name']
                        ?? $medication->name,

                'generic_name' =>
                    $data['generic_name']
                        ?? $medication->generic_name,

                'manufacturer' =>
                    $data['manufacturer']
                        ?? $medication->manufacturer,

                'dosage_form' =>
                    $data['dosage_form']
                        ?? $medication->dosage_form,

                'strength' =>
                    $data['strength']
                        ?? $medication->strength,

                'category' =>
                    $data['category']
                        ?? $medication->category,

                'requires_prescription' =>
                    $data['requires_prescription']
                        ?? $medication->requires_prescription,

                'side_effects' =>
                    $data['side_effects']
                        ?? $medication->side_effects,

                'interactions' =>
                    $data['interactions']
                        ?? $medication->interactions,

            ]);

            $medication->save();

            return $medication->fresh();

        });
    }

    /**
     * Delete medication.
     *
     * @throws ValidationException
     */
    public function deleteMedication(string $medicationId): void
    {
        DB::transaction(function () use ($medicationId) {

            $medication = $this->findMedication(
                $medicationId
            );

            if (
                $medication->prescriptions()->exists()
            ) {

                throw ValidationException::withMessages([
                    'medication' => [
                        'Medication cannot be deleted because it is used in one or more prescriptions.'
                    ]
                ]);

            }

            $medication->delete();

        });
    }

    /**
     * Prevent duplicate medications.
     *
     * @throws ValidationException
     */
    private function ensureMedicationDoesNotExist(
        string $name,
        ?string $strength,
        string $dosageForm,
        ?string $ignoreId = null
    ): void {

        $query = Medication::where(
            'name',
            $name
        )
        ->where(
            'dosage_form',
            $dosageForm
        )
        ->where(
            'strength',
            $strength
        );

        if ($ignoreId) {

            $query->where(
                'id',
                '!=',
                $ignoreId
            );

        }

        if ($query->exists()) {

            throw ValidationException::withMessages([
                'name' => [
                    'This medication already exists.'
                ]
            ]);

        }
    }
}