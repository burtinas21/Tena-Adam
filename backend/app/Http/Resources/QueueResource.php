<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'queue_number' => $this->queue_number,
            'status' => $this->status,
            'queue_date' => $this->queue_date,

            'patient' => $this->appointment?->patient_id,
            'doctor_id' => $this->doctor_id,

            'called_at' => $this->called_at,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,

            'appointment_id' => $this->appointment_id,

            'walk_in' => [
                'name' => $this->walk_in_patient_name,
                'phone' => $this->walk_in_phone,
            ],
        ];
    }
}