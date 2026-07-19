<?php

namespace App\Http\Requests\Api\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medication_id' => 'nullable|uuid|exists:medications,id',

            'medication_name' => 'sometimes|required|string|max:255',

            'dosage' => 'sometimes|required|string|max:50',

            'frequency' => 'sometimes|required|string|max:100',

            'route' => 'nullable|string|max:50',

            'duration_days' => 'nullable|integer|min:1',

            'quantity' => 'nullable|integer|min:1',

            'instructions' => 'nullable|string',

            'refills' => 'nullable|integer|min:0',

            'status' => 'sometimes|in:active,completed,cancelled',
        ];
    }
}