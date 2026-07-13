<?php

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [

            'slot_id' => [
                'required',
                'uuid',
                'exists:appointment_slots,id',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'slot_id.required' => 'Please select a new appointment slot.',

            'slot_id.exists' => 'Selected slot does not exist.',

        ];
    }
}