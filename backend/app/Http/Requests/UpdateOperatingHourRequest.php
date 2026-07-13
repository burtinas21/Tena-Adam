<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperatingHourRequest extends FormRequest
{

    public function authorize(): bool
    {

        return auth()->user()
            ->hasRole('hospital_admin');

    }



    public function rules(): array
    {

        return [

            'open_time'=>[
                'required'
            ],


            'close_time'=>[
                'required'
            ],


            'is_holiday'=>[
                'boolean'
            ],

        ];

    }
}