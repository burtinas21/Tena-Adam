<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'status' => $this->status,

            'encounter_id' => $this->encounter_id,

            'medication_id' => $this->medication_id,

            'medication_name' => $this->medication_name,

            'dosage' => $this->dosage,

            'frequency' => $this->frequency,

            'route' => $this->route,

            'duration_days' => $this->duration_days,

            'quantity' => $this->quantity,

            'instructions' => $this->instructions,

            'refills' => $this->refills,

            'encounter' => [

                'id' => $this->encounter?->id,

                'encounter_date' => $this->encounter?->encounter_date,

            ],

            'patient' => [

                'id' => $this->encounter?->patient?->id,

                'name' => trim(

                    ($this->encounter?->patient?->user?->first_name ?? '')
                    . ' ' .
                    ($this->encounter?->patient?->user?->last_name ?? '')

                ),

            ],

            'doctor' => [

                'id' => $this->encounter?->doctor?->id,

                'name' => trim(

                    ($this->encounter?->doctor?->user?->first_name ?? '')
                    . ' ' .
                    ($this->encounter?->doctor?->user?->last_name ?? '')

                ),

            ],

            'hospital' => [

                'id' => $this->encounter?->hospital?->id,

                'name' => $this->encounter?->hospital?->name,

            ],

            'medication' => $this->medication ? [

                'id' => $this->medication->id,

                'name' => $this->medication->name,

                'generic_name' => $this->medication->generic_name,

                'strength' => $this->medication->strength,

                'dosage_form' => $this->medication->dosage_form,

            ] : null,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}