<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'encounter_id'   => $this->encounter_id,
            'medication'     => [
                'id'   => $this->medication?->id,
                'name' => $this->medication?->name ?? $this->medication_name,
            ],
            'dosage'         => $this->dosage,
            'frequency'      => $this->frequency,
            'route'          => $this->route,
            'duration_days'  => $this->duration_days,
            'quantity'       => $this->quantity,
            'instructions'   => $this->instructions,
            'refills'        => $this->refills,
            'status'         => $this->status,
            'doctor'         => [
                'id'   => $this->encounter?->doctor?->id,
                'name' => $this->encounter?->doctor?->user?->name,
            ],
            'patient'        => [
                'id'   => $this->encounter?->patient?->id,
                'name' => $this->encounter?->patient?->name,
            ],
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
