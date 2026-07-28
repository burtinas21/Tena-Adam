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
            'latitude' => [
    'nullable',
    'numeric',
    'between:-90,90'
],


'longitude' => [
    'nullable',
    'numeric',
    'between:-180,180'
],


// 'google_place_id' => [
//     'nullable',
//     'string'
// ],


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