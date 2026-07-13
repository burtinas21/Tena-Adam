<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDoctorRequest extends FormRequest
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

            // User information

            'first_name'=>[
                'required',
                'string',
                'max:255'
            ],


            'last_name'=>[
                'required',
                'string',
                'max:255'
            ],


            'email'=>[
                'required',
                'email',
                'unique:users,email'
            ],


            'phone'=>[
                'nullable',
                'string',
                'max:20'
            ],


            'password'=>[
                'required',
                'string',
                'min:8'
            ],



            // Doctor profile

            'license_number'=>[
                'required',
                'string',
                'max:50',
                'unique:healthcare_providers,license_number'
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
            'practice_start_date'=>[

             'required',

             'date'

             ],


            'profile_picture' => [
            'required',
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