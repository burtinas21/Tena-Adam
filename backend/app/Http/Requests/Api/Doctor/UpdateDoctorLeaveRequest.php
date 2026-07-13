<?php

namespace App\Http\Requests\Api\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [

            'leave_date' => [
                'sometimes',
                'date',
                'after_or_equal:today',
            ],

            'leave_type' => [
                'sometimes',
                'in:vacation,sick,training,other',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'sometimes',
                'in:pending,approved,rejected',
            ],

        ];
    }
}