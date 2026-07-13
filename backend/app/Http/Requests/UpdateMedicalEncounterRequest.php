<?php

namespace App\Http\Requests\Api\Encounter;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalEncounterRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'chief_complaint' => [
                'sometimes',
                'required',
                'string',
            ],

            'history' => [
                'nullable',
                'string',
            ],

            'physical_exam' => [
                'nullable',
                'string',
            ],

            'assessment' => [
                'sometimes',
                'required',
                'string',
            ],

            'diagnosis' => [
                'sometimes',
                'required',
                'string',
            ],

            'diagnosis_icd10' => [
                'nullable',
                'string',
                'max:20',
            ],

            'treatment_plan' => [
                'nullable',
                'string',
            ],

            'clinical_notes' => [
                'nullable',
                'string',
            ],

            'follow_up_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'sometimes',
                'in:in_progress,completed,cancelled',
            ],

        ];
    }
}