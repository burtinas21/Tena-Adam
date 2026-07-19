<?php

namespace App\Http\Requests\Api\Notification;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserNotificationPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'email_enabled' => [
                'sometimes',
                'boolean',
            ],

            'sms_enabled' => [
                'sometimes',
                'boolean',
            ],

            'push_enabled' => [
                'sometimes',
                'boolean',
            ],

            'appointment_reminders' => [
                'sometimes',
                'boolean',
            ],

            'queue_updates' => [
                'sometimes',
                'boolean',
            ],

            'promotional' => [
                'sometimes',
                'boolean',
            ],

        ];
    }
}