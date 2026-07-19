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

            'encounter_id' => [
                'required',
                'uuid',
                'exists:medical_encounters,id',
            ],

            'patient_id' => [
                'required',
                'uuid',
                'exists:patients,id',
            ],

            'blood_pressure_systolic' => [
                'nullable',
                'integer',
                'min:40',
                'max:300',
            ],

            'blood_pressure_diastolic' => [
                'nullable',
                'integer',
                'min:20',
                'max:200',
            ],

            'pulse_rate' => [
                'nullable',
                'integer',
                'min:20',
                'max:250',
            ],

            'respiratory_rate' => [
                'nullable',
                'integer',
                'min:5',
                'max:80',
            ],

            'temperature' => [
                'nullable',
                'numeric',
                'between:30,45',
            ],

            'weight' => [
                'nullable',
                'numeric',
                'min:1',
                'max:500',
            ],

            'height' => [
                'nullable',
                'numeric',
                'min:30',
                'max:300',
            ],

            'blood_oxygen' => [
                'nullable',
                'integer',
                'between:0,100',
            ],

            'measured_at' => [
                'nullable',
                'date',
            ],

        ];
    }
}