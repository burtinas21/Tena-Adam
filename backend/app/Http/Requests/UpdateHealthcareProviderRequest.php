<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthcareProviderRequest extends FormRequest
{


    public function authorize(): bool
    {

        return true;

    }



    public function rules(): array
    {

        return [

            'license_number'=>[
                'sometimes',
                'string',
                'max:50'
            ],


            'department_id'=>[
                'sometimes',
                'uuid',
                'exists:departments,id'
            ],


            'consultation_fee'=>[
                'nullable',
                'numeric'
            ],


            'years_experience'=>[
                'nullable',
                'integer'
            ],


            'bio'=>[
                'nullable',
                'string'
            ],


          'profile_picture' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
            ],


            'is_telehealth_available'=>[
                'boolean'
            ]

        ];

    }


}