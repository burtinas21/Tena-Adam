<?php

namespace App\Http\Requests\Api\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmergencyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('patient');
    }

    public function rules(): array
    {
        return [

            'name' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'relationship' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'phone' => [
                'sometimes',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_primary' => [
                'sometimes',
                'boolean',
            ],

        ];
    }
}