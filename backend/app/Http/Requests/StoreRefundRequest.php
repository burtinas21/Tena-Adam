<?php

namespace App\Http\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;


class CreateRefundRequest extends FormRequest
{


    public function authorize(): bool
    {
        return true;
    }



    public function rules(): array
    {

        return [

            'payment_id'
            =>
            'required|uuid|exists:payments,id',


            'amount'
            =>
            'required|numeric|min:0',


            'reason'
            =>
            'nullable|string'

        ];

    }


}