<?php

namespace App\Http\Requests\Api\Specialization;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecializationRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('platform_admin');
    }


    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:100',
                'unique:specializations,name'
            ],


            'description' => [
                'nullable',
                'string'
            ],

        ];
    }



    public function messages(): array
    {
        return [

            'name.required'
                => 'Specialization name is required.',


            'name.unique'
                => 'This specialization already exists.',

        ];
    }

}