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
                'regex:/^\d{1,2}:\d{2}(:\d{2})?(\s?(AM|PM))?$/i',
            ],

            'end_time' => [
                'sometimes',
                'regex:/^\d{1,2}:\d{2}(:\d{2})?(\s?(AM|PM))?$/i',
            ],

            'slot_duration_min' => [
                'sometimes',
                'integer',
                'min:5',
                'max:240',
            ],

            'lunch_start' => [
                'nullable',
                'regex:/^\d{1,2}:\d{2}(:\d{2})?(\s?(AM|PM))?$/i',
            ],

            'lunch_end' => [
                'nullable',
                'regex:/^\d{1,2}:\d{2}(:\d{2})?(\s?(AM|PM))?$/i',
            ],

            'is_available' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.regex'   => 'The start time must be a valid time (e.g. 08:30 or 08:30 AM).',
            'end_time.regex'     => 'The end time must be a valid time (e.g. 16:30 or 04:30 PM).',
            'lunch_start.regex'  => 'The lunch start must be a valid time.',
            'lunch_end.regex'    => 'The lunch end must be a valid time.',
        ];
    }
}