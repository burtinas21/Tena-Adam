<?php

namespace App\Http\Requests\Api\Notification;
use Illuminate\Foundation\Http\FormRequest;
class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'user_id' => [
                'required',
                'uuid',
                'exists:users,id',
            ],

            'type' => [
                'required',
                'in:email,sms,push,in_app',
            ],

            'channel' => [
                'required',
                'string',
                'max:50',
            ],

            'subject' => [
                'nullable',
                'string',
                'max:255',
            ],

            'content' => [
                'required',
                'string',
            ],

            'status' => [
                'nullable',
                'in:pending,sent,failed,read',
            ],

        ];
    }
}