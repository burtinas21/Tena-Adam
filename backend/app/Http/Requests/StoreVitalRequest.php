<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVitalRequest extends FormRequest
{
    public function authorize(): bool
    {
    
        return true;
    }

    public function rules(): array
    {
        return [
            'encounter_id'            => ['required','uuid','exists:medical_encounters,id'],
            'patient_id'              => ['required','uuid','exists:patients,id'],
            'blood_pressure_systolic' => ['nullable','integer','min:50','max:250'],
            'blood_pressure_diastolic'=> ['nullable','integer','min:30','max:150'],
            'pulse_rate'              => ['nullable','integer','min:30','max:250'],
            'respiratory_rate'        => ['nullable','integer','min:5','max:60'],
            'temperature'             => ['nullable','numeric','between:30,45'],
            'weight'                  => ['nullable','numeric','between:1,500'],
            'height'                  => ['nullable','numeric','between:0.3,3.0'],
            'bmi'                     => ['nullable','numeric','between:10,60'],
            'blood_oxygen'            => ['nullable','integer','min:50','max:100'],
            'measured_at'             => ['nullable','date'],
        ];
    }
}
