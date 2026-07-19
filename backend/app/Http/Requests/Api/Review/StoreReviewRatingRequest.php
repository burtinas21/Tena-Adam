<?php

namespace App\Http\Requests\Api\Review;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRatingRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'patient_id' => [
                'required',
                'uuid',
                'exists:patients,id'
            ],

            'doctor_id' => [
                'required',
                'uuid',
                'exists:healthcare_providers,id'
            ],

            'appointment_id' => [
                'required',
                'uuid',
                'exists:appointments,id',
                'unique:review_ratings,appointment_id'
            ],

            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5'
            ],

            'comment' => [
                'nullable',
                'string'
            ],

            'is_anonymous' => [
                'boolean'
            ],

        ];
    }
}