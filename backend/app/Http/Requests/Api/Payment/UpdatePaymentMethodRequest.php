<?php

namespace App\Http\Requests\Api\Payment;


use Illuminate\Foundation\Http\FormRequest;


class CreatePaymentRequest extends FormRequest
{


    public function authorize(): bool
    {

        return true;

    }



    public function rules(): array
    {

        return [

            'appointment_id'
                =>
                'nullable|uuid|exists:appointments,id',


            'patient_id'
                =>
                'required|uuid|exists:patients,id',


            'hospital_id'
                =>
                'required|uuid|exists:hospitals,id',


            'amount'
                =>
                'required|numeric|min:0',


            'currency'
                =>
                'nullable|string|size:3',



            'payment_method'
                =>
                'required|string|max:50',



            'metadata'
                =>
                'nullable|array'

        ];

    }


}