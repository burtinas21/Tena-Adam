<?php

namespace App\Http\Requests\Api\Specialization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpecializationRequest extends FormRequest
{


    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->hasRole('platform_admin');
    }



    public function rules(): array
    {

        $specialization = $this->route('specialization');


        return [

            'name' => [

                'required',
                'string',
                'max:100',

                Rule::unique('specializations','name')
                    ->ignore($specialization->id)

            ],


            'description' => [

                'nullable',
                'string'

            ],

        ];

    }


}