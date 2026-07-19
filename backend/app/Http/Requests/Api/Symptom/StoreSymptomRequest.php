<?php

namespace App\Http\Requests\Api\Symptom;

use Illuminate\Foundation\Http\FormRequest;

class StoreSymptomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy will handle authorization
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:symptoms,name',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:50',
        ];
    }
}
