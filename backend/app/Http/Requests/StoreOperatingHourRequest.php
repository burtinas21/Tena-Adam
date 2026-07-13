<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOperatingHourRequest extends FormRequest
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


            'day_of_week'=>[
                'required',
                'integer',
                'between:0,6'
            ],


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