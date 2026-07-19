<?php

namespace App\Http\Requests\Api\Symptom;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSymptomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy will handle authorization
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:100|unique:symptoms,name,' . $this->route('id'),
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
        ];
    }
}
