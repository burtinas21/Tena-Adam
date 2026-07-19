<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'blood_pressure_systolic' => [
                'sometimes',
                'nullable',
                'integer',
                'min:40',
                'max:300',
            ],

            'blood_pressure_diastolic' => [
                'sometimes',
                'nullable',
                'integer',
                'min:20',
                'max:200',
            ],

            'pulse_rate' => [
                'sometimes',
                'nullable',
                'integer',
                'min:20',
                'max:250',
            ],

            'respiratory_rate' => [
                'sometimes',
                'nullable',
                'integer',
                'min:5',
                'max:80',
            ],

            'temperature' => [
                'sometimes',
                'nullable',
                'numeric',
                'between:30,45',
            ],

            'weight' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:1',
                'max:500',
            ],

            'height' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:30',
                'max:300',
            ],

            'blood_oxygen' => [
                'sometimes',
                'nullable',
                'integer',
                'between:0,100',
            ],

            'measured_at' => [
                'sometimes',
                'nullable',
                'date',
            ],

        ];
    }
}