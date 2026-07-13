<?php

namespace App\Http\Requests;
use App\Models\User;
use App\Models\Department;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthcareProviderRequest extends FormRequest
{


    public function authorize(): bool
    {

        return auth()
            ->user()
            ->hasRole('hospital_admin');

    }



    public function rules(): array
    {

        return [

            'id'=>[
                'required',
                'uuid',
                'exists:users,id'
            ],


            'license_number'=>[
                'required',
                'string',
                'max:50',
                'unique:healthcare_providers'
            ],


            'department_id'=>[
                'required',
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
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
        ],
             'practice_start_date'=>[

             'required',

             'date'

             ],

            'is_telehealth_available'=>[
                'boolean'
            ]

        ];

    }
    

}