<?php

namespace App\Http\Requests\Api\Telehealth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTelehealthSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'session_url' => [
                'sometimes',
                'url',
                'max:500',
            ],

            'platform' => [
                'sometimes',
                'in:google_meet,zoom,microsoft_teams,custom',
            ],

            'room_id' => [
                'nullable',
                'string',
                'max:100',
            ],

            'meeting_id' => [
                'nullable',
                'string',
                'max:100',
            ],

            'recording_url' => [
                'nullable',
                'url',
                'max:500',
            ],

            'recording_consent' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'sometimes',
                'in:scheduled,active,completed,cancelled',
            ],
        ];
    }
}