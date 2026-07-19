<?php

namespace App\Http\Requests\Api\Symptom;

use Illuminate\Foundation\Http\FormRequest;

class StoreSymptomDepartmentMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    public function rules(): array
    {
        return [
            'symptom_id' => 'required|uuid|exists:symptoms,id',
            'department_id' => 'required|uuid|exists:departments,id',
            'relevance_score' => 'nullable|integer|min:0|max:100',
            'is_primary' => 'nullable|boolean',
            'evidence_level' => 'nullable|in:high,medium,low',
        ];
    }
}
