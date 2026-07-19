<?php

namespace App\Http\Requests\Api\Telehealth;

use Illuminate\Foundation\Http\FormRequest;

class StoreTelehealthAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // You can later enforce policies here
    }

    public function rules(): array
    {
        return [
            'session_id' => [
                'required',
                'uuid',
                'exists:telehealth_sessions,id',
            ],

            'user_id' => [
                'required',
                'uuid',
                'exists:users,id',
            ],

            'joined_at' => [
                'nullable',
                'date',
            ],

            'left_at' => [
                'nullable',
                'date',
            ],

            'device_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'ip_address' => [
                'nullable',
                'string',
                'max:45',
            ],
        ];
    }
}
