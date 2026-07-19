<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelehealthSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'appointment_id' => $this->appointment_id,

            'platform' => $this->platform,

            'session_url' => $this->session_url,

            'room_id' => $this->room_id,

            'meeting_id' => $this->meeting_id,

            'started_at' => $this->started_at,

            'ended_at' => $this->ended_at,

            'duration_min' => $this->duration_min,

            'recording_url' => $this->recording_url,

            'recording_consent' => $this->recording_consent,

            'status' => $this->status,

            'appointment' => [
                'id' => $this->appointment?->id,
                'scheduled_time' => $this->appointment?->scheduled_time,
                'status' => $this->appointment?->status,
            ],

            'patient' => [
                'id' => $this->appointment?->patient?->id,
                'name' => trim(
                    ($this->appointment?->patient?->first_name ?? '') . ' ' .
                    ($this->appointment?->patient?->last_name ?? '')
                ),
            ],

            'doctor' => [
                'id' => $this->appointment?->doctor?->id,
                'name' => trim(
                    ($this->appointment?->doctor?->user?->first_name ?? '') . ' ' .
                    ($this->appointment?->doctor?->user?->last_name ?? '')
                ),
            ],

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,
        ];
    }
}