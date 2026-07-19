<?php

namespace App\Http\Requests\Api\Telehealth;

use Illuminate\Foundation\Http\FormRequest;

class StoreTelehealthSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|uuid|exists:healthcare_providers,id',
            'appointment_id' => 'required|uuid|exists:appointments,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'platform' => 'required|in:google_meet,zoom,microsoft_teams,custom',
            'session_url' => 'nullable|url|max:500',
            'room_id' => 'nullable|string|max:100',
            'meeting_id' => 'nullable|string|max:100',
            'recording_url' => 'nullable|url|max:500',
            'recording_consent' => 'nullable|boolean',
        ];
    }

    /**
     * Merge the authenticated doctor's ID into the validated data automatically.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'doctor_id' => auth()->id(),
        ]);
    }
}
