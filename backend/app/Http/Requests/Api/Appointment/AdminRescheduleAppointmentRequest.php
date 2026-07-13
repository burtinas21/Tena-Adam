<?php

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class AdminRescheduleAppointmentRequest extends FormRequest
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
            'slot_id.required' => 'Please select a replacement slot.',
            'slot_id.uuid'     => 'Invalid slot identifier.',
            'slot_id.exists'   => 'The selected slot does not exist.',
        ];
    }
}
