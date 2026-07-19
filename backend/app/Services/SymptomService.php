<?php

namespace App\Services;

use App\Models\Symptom;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class SymptomService
{
    /**
     * Store a new symptom.
     */
    public function store(array $data): Symptom
    {
        return Symptom::create($data);
    }

    /**
     * Update an existing symptom.
     */
    public function update(string $id, array $data): Symptom
    {
        try {
            $symptom = Symptom::findOrFail($id);
            $symptom->update($data);

            return $symptom->fresh();
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'symptom' => ['Symptom not found.'],
            ]);
        }
    }

    /**
     * Find a symptom.
     */
    public function find(string $id): Symptom
    {
        try {
            return Symptom::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'symptom' => ['Symptom not found.'],
            ]);
        }
    }

    /**
     * Delete a symptom.
     */
    public function delete(string $id): void
    {
        try {
            $symptom = Symptom::findOrFail($id);
            $symptom->delete();
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages([
                'symptom' => ['Symptom not found.'],
            ]);
        }
    }
}
