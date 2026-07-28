<?php

namespace App\Http\Requests\Api\Hospital;

use Illuminate\Foundation\Http\FormRequest;


class StoreHospitalRequest extends FormRequest
{


    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {

        return [

            'name'=>[
                'required',
                'string',
                'max:255',
                'unique:hospitals,name'
            ],


            'code'=>[
                'nullable',
                'string',
                'max:20',
                'unique:hospitals,code'
            ],


            'address'=>[
                'required',
                'string'
            ],


            'city'=>[
                'required',
                'string'
            ],


            'region'=>[
                'nullable',
                'string'
            ],


            'phone'=>[
                'nullable',
                'string',
                'max:20'
            ],


            'email'=>[
                'nullable',
                'email'
            ],


            'website'=>[
                'nullable',
                'string'
            ],


            'logo_url'=>[
                'nullable',
                'string'
            ],


            'registration_number'=>[
                'nullable',
                'string'
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

        ];

    }

}