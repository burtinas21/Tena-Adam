<?php

namespace App\Http\Requests\Api\Symptom;

use Illuminate\Foundation\Http\FormRequest;

class StoreSymptomAnalyticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    public function rules(): array
    {
        return [
            'symptom_id'               => 'required|uuid|exists:symptoms,id',
            'recommended_department_id' => 'required|uuid|exists:departments,id',
            'selected_by_patient'      => 'nullable|boolean',
            'patient_id'               => 'nullable|uuid|exists:patients,id',
            'session_id'               => 'nullable|uuid',
        ];
    }
}
