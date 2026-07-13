<?php

namespace App\Http\Requests\Api\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('patient');
    }

    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'relationship' => [
                'required',
                'string',
                'max:50',
            ],

            'phone' => [
                'required',
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

    public function messages(): array
    {
        return [

            'name.required' =>
                'Emergency contact name is required.',

            'relationship.required' =>
                'Relationship is required.',

            'phone.required' =>
                'Phone number is required.',

        ];
    }
}