<?php

namespace App\Http\Requests\Api\Telehealth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTelehealthAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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
