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
            'blood_pressure_systolic' => ['sometimes','integer','min:50','max:250'],
            'blood_pressure_diastolic'=> ['sometimes','integer','min:30','max:150'],
            'pulse_rate'              => ['sometimes','integer','min:30','max:250'],
            'respiratory_rate'        => ['sometimes','integer','min:5','max:60'],
            'temperature'             => ['sometimes','numeric','between:30,45'],
            'weight'                  => ['sometimes','numeric','between:1,500'],
            'height'                  => ['sometimes','numeric','between:0.3,3.0'],
            'bmi'                     => ['sometimes','numeric','between:10,60'],
            'blood_oxygen'            => ['sometimes','integer','min:50','max:100'],
            'measured_at'             => ['sometimes','date'],
        ];
    }
}
