<?php

namespace App\Http\Requests\Api\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{

    public function authorize(): bool
    {

        return auth()->user()
            ->hasRole('hospital_admin');

    }



    public function rules(): array
    {

        return [

            'name'=>[
                'required',
                'string',
                'max:100'
            ],


            'description'=>[
                'nullable',
                'string'
            ],


            'head_doctor_id'=>[
                'nullable',
                'exists:healthcare_providers,id'
            ],


            'parent_department_id'=>[
                'nullable',
                'exists:departments,id'
            ],


            'is_active'=>[
                'boolean'
            ],

        ];

    }
}