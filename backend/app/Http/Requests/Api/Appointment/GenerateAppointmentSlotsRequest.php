<?php

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAppointmentSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('hospital_admin')
            || auth()->user()->hasRole('platform_admin');
    }

    public function rules(): array
    {
        return [

            'doctor_id' => [
                'required',
                'uuid',
                'exists:healthcare_providers,id',
            ],

            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'doctor_id.required' =>
                'Doctor is required.',

            'doctor_id.exists' =>
                'Selected doctor does not exist.',

            'date.required' =>
                'Date is required.',

            'date.after_or_equal' =>
                'Date cannot be in the past.',

        ];
    }
}