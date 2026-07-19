<?php

namespace App\Http\Requests\Api\Encounter;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEncounterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chief_complaint' => ['nullable', 'string'],
            'history'         => ['nullable', 'string'],
            'physical_exam'   => ['nullable', 'string'],
            'assessment'      => ['nullable', 'string'],
            'diagnosis'       => ['nullable', 'string'],
            'diagnosis_icd10' => ['nullable', 'string', 'max:20'],
            'treatment_plan'  => ['nullable', 'string'],
            'clinical_notes'  => ['nullable', 'string'],
            'follow_up_date'  => ['nullable', 'date'],
        ];
    }
}
