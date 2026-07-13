<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MedicalEncounterResource extends JsonResource
{


    public function toArray(Request $request): array
    {

        return [

            'id'=>$this->id,


            /*
            |--------------------------------------------------------------------------
            | Patient
            |--------------------------------------------------------------------------
            */

            'patient'=>[

                'id'=>$this->patient?->id,

                'name'=>
                    $this->patient?->name,

            ],



            /*
            |--------------------------------------------------------------------------
            | Doctor
            |--------------------------------------------------------------------------
            */


            'doctor'=>[

                'id'=>$this->doctor?->id,

                'name'=>
                    $this->doctor?->user?->name,

            ],




            /*
            |--------------------------------------------------------------------------
            | Hospital
            |--------------------------------------------------------------------------
            */


            'hospital'=>[

                'id'=>$this->hospital?->id,

                'name'=>$this->hospital?->name,

            ],






            /*
            |--------------------------------------------------------------------------
            | Appointment
            |--------------------------------------------------------------------------
            */


            'appointment_id'=>
                $this->appointment_id,




            /*
            |--------------------------------------------------------------------------
            | Encounter Information
            |--------------------------------------------------------------------------
            */


            'encounter_date'=>
                $this->encounter_date,


            'status'=>
                $this->status,



            /*
            |--------------------------------------------------------------------------
            | Clinical Data
            |--------------------------------------------------------------------------
            */


            'clinical'=>[


                'chief_complaint'=>
                    $this->chief_complaint,


                'history'=>
                    $this->history,


                'physical_exam'=>
                    $this->physical_exam,


                'assessment'=>
                    $this->assessment,


                'diagnosis'=>
                    $this->diagnosis,


                'diagnosis_icd10'=>
                    $this->diagnosis_icd10,


                'treatment_plan'=>
                    $this->treatment_plan,


                'clinical_notes'=>
                    $this->clinical_notes,


                'follow_up_date'=>
                    $this->follow_up_date,


                'vitals'=>
                    $this->vitals,

            ],



            'created_at'=>
                $this->created_at,


            'updated_at'=>
                $this->updated_at,


        ];

    }


}