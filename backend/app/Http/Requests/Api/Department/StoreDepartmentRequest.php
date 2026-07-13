<?php

namespace App\Http\Requests\Api\Department;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{

    public function authorize(): bool
    {

        return auth()->user()
            ->hasRole('hospital_admin');

    }



    public function rules(): array
    {

        return [

            'hospital_id'=>[
                'required',
                'exists:hospitals,id'
            ],


            'name'=>[
                'required',
                'string',
                'max:100'
            ],


            'description'=>[
                'nullable',
                'string'
            ],


            'parent_department_id'=>[
                'nullable',
                'exists:departments,id'
            ],

            'head_doctor_id'=>[
                'nullable',
                'exists:healthcare_providers,id'
            ],

        ];

    }
}