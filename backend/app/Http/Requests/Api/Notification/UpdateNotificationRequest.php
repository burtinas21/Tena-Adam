<?php

namespace App\Http\Requests\Api\Notification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'status' => [
                'sometimes',
                'in:pending,sent,failed,read',
            ],

            'error_message' => [
                'nullable',
                'string',
            ],

            'retry_count' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ];
    }
}