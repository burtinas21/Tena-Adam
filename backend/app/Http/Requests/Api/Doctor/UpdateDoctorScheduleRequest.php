<?php

namespace App\Http\Requests\Api\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorScheduleRequest extends FormRequest
{
   public function authorize(): bool
{
    return auth()->check();
}

    public function rules(): array
    {
        return [
            'start_time' => [
                'sometimes',
                'date_format:H:i',
            ],

            'end_time' => [
                'sometimes',
                'date_format:H:i',
            ],

            'slot_duration_min' => [
                'sometimes',
                'integer',
                'min:5',
                'max:240',
            ],

            'lunch_start' => [
                'nullable',
                'date_format:H:i',
            ],

            'lunch_end' => [
                'nullable',
                'date_format:H:i',
            ],

            'is_available' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}