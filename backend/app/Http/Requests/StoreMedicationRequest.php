<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

            'generic_name' => 'nullable|string|max:255',

            'manufacturer' => 'nullable|string|max:255',

            'dosage_form' => 'required|string|max:50',

            'strength' => 'nullable|string|max:50',

            'category' => 'nullable|string|max:100',

            'requires_prescription' => 'boolean',

            'side_effects' => 'nullable|string',

            'interactions' => 'nullable|string',
        ];
    }
}