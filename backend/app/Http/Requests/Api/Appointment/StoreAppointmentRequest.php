<?php

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('patient')
            || auth()->user()->hasRole('receptionist')
            || auth()->user()->hasRole('platform_admin');
    }

    public function rules(): array
    {
        return [
            'patient_id' => [
                'nullable',
                'uuid',
                'exists:patients,id',
            ],

            'doctor_id' => [
                'required',
                'uuid',
                'exists:healthcare_providers,id',
            ],

            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'appointment_time' => [
                'required',
                'date_format:H:i',
            ],

            'reason' => [
                'required',
                'string',
                'max:1000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_telehealth' => [
                'boolean',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.required'          => 'Please select a doctor.',
            'appointment_date.required'   => 'Appointment date is required.',
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past.',
            'appointment_time.required'   => 'Appointment time is required.',
            'appointment_time.date_format'=> 'Time must be in HH:MM format.',
        ];
    }
}
