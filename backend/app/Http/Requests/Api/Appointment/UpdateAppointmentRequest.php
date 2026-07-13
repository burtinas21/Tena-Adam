<?php

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('patient')
            || auth()->user()->hasRole('doctor')
            || auth()->user()->hasRole('hospital_admin')
            || auth()->user()->hasRole('platform_admin');
    }

    public function rules(): array
    {
        return [

            // Reschedule fields
            'appointment_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],

            'appointment_time' => [
                'sometimes',
                'required_with:appointment_date',
                'date_format:H:i',
            ],

            // Status change
            'status' => [
                'sometimes',
                'in:pending,confirmed,cancelled,completed,no_show',
            ],

            // Doctor/admin notes
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Reason update
            'reason' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Cancellation reason
            'cancel_reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
