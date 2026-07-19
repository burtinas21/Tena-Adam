<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'encounter_id' => $this->encounter_id,

            'patient_id' => $this->patient_id,

            'blood_pressure' => [
                'systolic' => $this->blood_pressure_systolic,
                'diastolic' => $this->blood_pressure_diastolic,
            ],

            'pulse_rate' => $this->pulse_rate,

            'respiratory_rate' => $this->respiratory_rate,

            'temperature' => $this->temperature,

            'weight' => $this->weight,

            'height' => $this->height,

            'bmi' => $this->bmi,

            'blood_oxygen' => $this->blood_oxygen,

            'measured_at' => $this->measured_at,

            'patient' => [

                'id' => $this->patient?->id,

                'name' => trim(
                    ($this->patient?->user?->first_name ?? '') .
                    ' ' .
                    ($this->patient?->user?->last_name ?? '')
                ),

            ],

            'doctor' => [

                'id' => $this->encounter?->doctor?->id,

                'name' => trim(
                    ($this->encounter?->doctor?->user?->first_name ?? '') .
                    ' ' .
                    ($this->encounter?->doctor?->user?->last_name ?? '')
                ),

            ],

            'hospital' => [

                'id' => $this->encounter?->hospital?->id,

                'name' => $this->encounter?->hospital?->name,

            ],

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}