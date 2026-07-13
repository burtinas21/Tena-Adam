<?php

namespace App\Http\Requests\Api\Patient;

use Illuminate\Foundation\Http\FormRequest;

class CompletePatientProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('patient');
    }

    public function rules(): array
    {
        return [

            'address' => [
                'required',
                'string',
                'max:1000',
            ],

            'occupation' => [
                'required',
                'string',
                'max:100',
            ],

            'national_id' => [
                'nullable',
                'string',
                'max:20',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'address.required' =>
                'Address is required.',

            'occupation.required' =>
                'Occupation is required.',

        ];
    }
}