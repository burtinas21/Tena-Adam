<?php

namespace App\Http\Requests\Api\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by PrescriptionPolicy
        return true;
    }

    public function rules(): array
    {
        return [
            'encounter_id'    => ['required','uuid','exists:medical_encounters,id'],
            'medication_id'   => ['nullable','uuid','exists:medications,id'],
            'medication_name' => ['required','string','max:255'],
            'dosage'          => ['required','string','max:50'],
            'frequency'       => ['required','string','max:100'],
            'route'           => ['nullable','string','max:50'],
            'duration_days'   => ['nullable','integer','min:1'],
            'quantity'        => ['nullable','integer','min:1'],
            'instructions'    => ['nullable','string'],
            'refills'         => ['nullable','integer','min:0'],
            'status'          => ['nullable','in:active,completed,cancelled'],
        ];
    }
}
