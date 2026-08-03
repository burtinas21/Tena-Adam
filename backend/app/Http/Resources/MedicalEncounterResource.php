<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VitalResource;

class MedicalEncounterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'status' => $this->status,

            'encounter_date' => $this->encounter_date,

            /*
            |--------------------------------------------------------------------------
            | Patient
            |--------------------------------------------------------------------------
            */

            'patient' => [

                'id' => $this->patient?->id,

                'first_name' => $this->patient?->user?->first_name,

                'last_name' => $this->patient?->user?->last_name,

                'email' => $this->patient?->user?->email,

                'phone' => $this->patient?->user?->phone,

                'gender' => $this->patient?->gender,

                'date_of_birth' => $this->patient?->date_of_birth,

                'blood_type' => $this->patient?->blood_type,

                'allergies' => $this->patient?->allergies,

                'medical_history' => $this->patient?->medical_history,

            ],

            // Walk-in patient fields (populated when no registered patient record)
            'walk_in_patient_name' => $this->walk_in_patient_name,

            'walk_in_phone' => $this->walk_in_phone,

            /*
            |--------------------------------------------------------------------------
            | Doctor
            |--------------------------------------------------------------------------
            */

            'doctor' => [

                'id' => $this->doctor?->id,

                'first_name' => $this->doctor?->user?->first_name,

                'last_name' => $this->doctor?->user?->last_name,

                'email' => $this->doctor?->user?->email,

            ],

            /*
            |--------------------------------------------------------------------------
            | Hospital
            |--------------------------------------------------------------------------
            */

            'hospital' => [

                'id' => $this->hospital?->id,

                'name' => $this->hospital?->name,

            ],

            /*
            |--------------------------------------------------------------------------
            | Appointment
            |--------------------------------------------------------------------------
            */

            'appointment' => [

                'id' => $this->appointment?->id,

                'scheduled_time' => $this->appointment?->scheduled_time,

                'status' => $this->appointment?->status,

            ],

            /*
            |--------------------------------------------------------------------------
            | Medical Record
            |--------------------------------------------------------------------------
            */

            'chief_complaint' => $this->chief_complaint,

            'history' => $this->history,

            'physical_exam' => $this->physical_exam,

            'assessment' => $this->assessment,

            'diagnosis' => $this->diagnosis,

            'diagnosis_icd10' => $this->diagnosis_icd10,

            'treatment_plan' => $this->treatment_plan,

            'clinical_notes' => $this->clinical_notes,

            'follow_up_date' => $this->follow_up_date,

            'vitals' => $this->vital ? new VitalResource($this->vital) : null,

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}