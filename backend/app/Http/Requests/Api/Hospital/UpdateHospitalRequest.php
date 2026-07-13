<?php

namespace App\Http\Requests\Api\Hospital;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHospitalRequest extends FormRequest
{

    public function authorize(): bool
    {

        return auth()->user()
            ->hasRole('platform_admin');

    }



    public function rules(): array
    {

        return [

            'name' =>
            [
                'required',
                'string',
                'max:255',
            ],


            'address' =>
            [
                'required',
                'string',
            ],


            'city' =>
            [
                'required',
                'string',
            ],


            'phone' =>
            [
                'nullable',
                'string'
            ],


            'email' =>
            [
                'nullable',
                'email'
            ],

        ];

    }
}