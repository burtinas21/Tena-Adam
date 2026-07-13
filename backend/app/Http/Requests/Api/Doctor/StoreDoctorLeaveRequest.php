<?php

namespace App\Http\Requests\Api\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('doctor');
    }

    public function rules(): array
    {
        return [

            'leave_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'leave_type' => [
                'required',
                'in:vacation,sick,training,other',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],

        ];
    }

    public function messages(): array
    {
        return [

            'leave_date.after_or_equal' =>
                'Leave date cannot be in the past.',

            'leave_type.in' =>
                'Invalid leave type.',

        ];
    }
}