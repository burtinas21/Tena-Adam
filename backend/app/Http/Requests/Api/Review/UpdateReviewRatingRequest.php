<?php

namespace App\Http\Requests\Api\Review;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRatingRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'rating' => [
                'sometimes',
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