<?php

namespace App\Http\Requests\Api\SymptomDepartmentMapping;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|uuid|exists:patients,id',
            'scheduled_at' => 'required|date|after:now',
        ];
    }
}
