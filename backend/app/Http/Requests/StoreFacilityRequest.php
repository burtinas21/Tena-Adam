<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
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
                'string'
            ],


            'type'=>[
                'required',
                'in:room,bed,clinic,lab,pharmacy'
            ],


            'status'=>[
                'nullable',
                'in:available,occupied,maintenance,reserved'
            ],


            'description'=>[
                'nullable',
                'string'
            ],

        ];

    }
}