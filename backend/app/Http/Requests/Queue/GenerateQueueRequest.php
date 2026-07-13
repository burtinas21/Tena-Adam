<?php

namespace App\Http\Requests\Queue;

use Illuminate\Foundation\Http\FormRequest;

class GenerateQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy will handle real authorization
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|uuid|exists:healthcare_providers,id',
            'date' => 'required|date',
        ];
    }
}