<?php

namespace App\Http\Requests\Api\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('hospital_admin')
            || auth()->user()->hasRole('doctor');
    }

    public function rules(): array
    {
        return [

            // hospital_admin must supply doctor_id; doctor uses their own id
            'doctor_id' => [
                'nullable',
                'uuid',
                'exists:healthcare_providers,id',
            ],

            'day_of_week' => [
                'required',
                'integer',
                'between:0,6',
            ],

            'start_time' => [
                'required',
                'regex:/^\d{1,2}:\d{2}(:\d{2})?(\s?(AM|PM))?$/i',
            ],

            'end_time' => [
                'required',
                'regex:/^\d{1,2}:\d{2}(:\d{2})?(\s?(AM|PM))?$/i',
            ],

            'slot_duration_min' => [
                'nullable',
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
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [

            'day_of_week.between' =>
                'Day must be between 0 (Sunday) and 6 (Saturday).',

            'start_time.before' =>
                'Start time must be before end time.',

            'end_time.after' =>
                'End time must be after start time.',

            'lunch_end.after' =>
                'Lunch end must be after lunch start.',
        ];
    }
}