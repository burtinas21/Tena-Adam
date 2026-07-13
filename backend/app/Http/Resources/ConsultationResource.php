<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'queue' => [
                'id' => $this->id,
                'queue_number' => $this->queue_number,
                'queue_date' => $this->queue_date,
                'status' => $this->status,
                'started_at' => $this->started_at,
            ],

            'appointment' => [
                'id' => $this->appointment?->id,
                'scheduled_time' => $this->appointment?->scheduled_time,
                'reason' => $this->appointment?->reason,
                'notes' => $this->appointment?->notes,
                'status' => $this->appointment?->status,
            ],

            'patient' => [
                'id' => $this->appointment?->patient?->id,

                'first_name' =>
                    $this->appointment?->patient?->user?->first_name,

                'last_name' =>
                    $this->appointment?->patient?->user?->last_name,

                'email' =>
                    $this->appointment?->patient?->user?->email,

                'phone' =>
                    $this->appointment?->patient?->user?->phone,

                'date_of_birth' =>
                    $this->appointment?->patient?->date_of_birth,

                'gender' =>
                    $this->appointment?->patient?->gender,

                'blood_type' =>
                    $this->appointment?->patient?->blood_type,

                'allergies' =>
                    $this->appointment?->patient?->allergies,

                'medical_history' =>
                    $this->appointment?->patient?->medical_history,

                'occupation' =>
                    $this->appointment?->patient?->occupation,
            ],

            'doctor' => [
                'id' => $this->appointment?->doctor?->id,

                'first_name' =>
                    $this->appointment?->doctor?->user?->first_name,

                'last_name' =>
                    $this->appointment?->doctor?->user?->last_name,

                'license_number' =>
                    $this->appointment?->doctor?->license_number,
            ],

            'hospital' => [
                'id' => $this->appointment?->hospital?->id,
                'name' => $this->appointment?->hospital?->name,
            ],

            'department' => [
                'id' => $this->appointment?->department?->id,
                'name' => $this->appointment?->department?->name,
            ],
        ];
    }
}