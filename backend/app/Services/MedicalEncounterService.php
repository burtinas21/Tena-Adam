<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MedicalEncounter;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;


class MedicalEncounterService
{

    /*
    |--------------------------------------------------------------------------
    | Create Encounter
    |--------------------------------------------------------------------------
    */

    public function createEncounter(array $data): MedicalEncounter
    {
        return DB::transaction(function () use ($data) {

            $appointment = Appointment::findOrFail(
                $data['appointment_id']
            );


            /*
            |--------------------------------------------------------------------------
            | Duplicate encounter prevention
            |--------------------------------------------------------------------------
            */

            if (
                MedicalEncounter::where(
                    'appointment_id',
                    $appointment->id
                )->exists()
            ) {

                throw ValidationException::withMessages([

                    'appointment_id' =>
                    [
                        'Medical encounter already exists for this appointment.'
                    ]

                ]);

            }



            /*
            |--------------------------------------------------------------------------
            | Doctor ownership verification
            |--------------------------------------------------------------------------
            */

            if (
                $appointment->doctor_id !== $data['doctor_id']
            ) {

                throw ValidationException::withMessages([

                    'doctor_id' =>
                    [
                        'Doctor does not own this appointment.'
                    ]

                ]);

            }



            /*
            |--------------------------------------------------------------------------
            | Queue verification
            |--------------------------------------------------------------------------
            */

            $queue = Queue::where(
                'appointment_id',
                $appointment->id
            )->first();



            if (!$queue) {

                throw ValidationException::withMessages([

                    'queue' =>
                    [
                        'Patient queue record not found.'
                    ]

                ]);

            }



            if (
                $queue->status !== 'in_consultation'
            ) {


                throw ValidationException::withMessages([

                    'queue' =>
                    [
                        'Patient is not currently in consultation.'
                    ]

                ]);

            }



            $encounter = MedicalEncounter::create([

                'patient_id' =>
                    $appointment->patient_id,

                'doctor_id' =>
                    $appointment->doctor_id,

                'hospital_id' =>
                    $appointment->hospital_id,

                'appointment_id' =>
                    $appointment->id,


                'encounter_date' =>
                    now(),


                'chief_complaint' =>
                    $data['chief_complaint'],

                'history' =>
                    $data['history'] ?? null,

                'physical_exam' =>
                    $data['physical_exam'] ?? null,

                'assessment' =>
                    $data['assessment'],

                'diagnosis' =>
                    $data['diagnosis'],

                'diagnosis_icd10' =>
                    $data['diagnosis_icd10'] ?? null,


                'treatment_plan' =>
                    $data['treatment_plan'] ?? null,

                'clinical_notes' =>
                    $data['clinical_notes'] ?? null,


                'follow_up_date' =>
                    $data['follow_up_date'] ?? null,


                'status' =>
                    'in_progress'

            ]);



            return $this->loadEncounterRelations($encounter);

        });
    }







    /*
    |--------------------------------------------------------------------------
    | Find Encounter
    |--------------------------------------------------------------------------
    |
    | Get single medical encounter
    |
    */

    public function findEncounter(string $id): MedicalEncounter
    {

        try {


            $encounter = MedicalEncounter::with([

                'patient.user',

                'doctor.user',

                'hospital',

                'appointment',

            ])
            ->findOrFail($id);



            return $encounter;



        } catch (ModelNotFoundException $e) {


            throw ValidationException::withMessages([

                'encounter' =>
                [
                    'Medical encounter not found.'
                ]

            ]);

        }

    }








    /*
    |--------------------------------------------------------------------------
    | Update Encounter
    |--------------------------------------------------------------------------
    |
    | Update medical information during consultation
    |
    */

    public function updateEncounter(
        string $id,
        array $data
    ): MedicalEncounter {


        return DB::transaction(function () use ($id,$data) {


            $encounter = MedicalEncounter::findOrFail($id);



            /*
            |--------------------------------------------------------------------------
            | Prevent editing completed records
            |--------------------------------------------------------------------------
            */

            if (
                $encounter->status === 'completed'
            ) {


                throw ValidationException::withMessages([

                    'encounter' =>
                    [
                        'Completed medical records cannot be modified.'
                    ]

                ]);

            }




            /*
            |--------------------------------------------------------------------------
            | Update allowed fields
            |--------------------------------------------------------------------------
            */

            $encounter->update([


                'chief_complaint' =>
                    $data['chief_complaint']
                    ??
                    $encounter->chief_complaint,


                'history' =>
                    $data['history']
                    ??
                    $encounter->history,


                'physical_exam' =>
                    $data['physical_exam']
                    ??
                    $encounter->physical_exam,


                'assessment' =>
                    $data['assessment']
                    ??
                    $encounter->assessment,


                'diagnosis' =>
                    $data['diagnosis']
                    ??
                    $encounter->diagnosis,


                'diagnosis_icd10' =>
                    $data['diagnosis_icd10']
                    ??
                    $encounter->diagnosis_icd10,


                'treatment_plan' =>
                    $data['treatment_plan']
                    ??
                    $encounter->treatment_plan,


                'clinical_notes' =>
                    $data['clinical_notes']
                    ??
                    $encounter->clinical_notes,


                'follow_up_date' =>
                    $data['follow_up_date']
                    ??
                    $encounter->follow_up_date,


            ]);




            return $this->loadEncounterRelations(
                $encounter
            );


        });

    }








    /*
    |--------------------------------------------------------------------------
    | Complete Encounter
    |--------------------------------------------------------------------------
    |
    | Finalize consultation
    |
    */

    public function completeEncounter(
        string $id
    ): MedicalEncounter {


        return DB::transaction(function () use ($id) {


            $encounter =
                MedicalEncounter::with('appointment')
                ->findOrFail($id);



            if (
                $encounter->status === 'completed'
            ) {


                throw ValidationException::withMessages([

                    'encounter' =>
                    [
                        'Encounter already completed.'
                    ]

                ]);

            }




            /*
            |--------------------------------------------------------------------------
            | Complete encounter
            |--------------------------------------------------------------------------
            */

            $encounter->update([

                'status'=>'completed'

            ]);






            /*
            |--------------------------------------------------------------------------
            | Update Queue
            |--------------------------------------------------------------------------
            */

            $queue = Queue::where(

                'appointment_id',

                $encounter->appointment_id

            )->first();



            if($queue){


                $queue->update([

                    'status'=>'completed',

                    'ended_at'=>now()

                ]);

            }






            /*
            |--------------------------------------------------------------------------
            | Update Appointment
            |--------------------------------------------------------------------------
            */

            if($encounter->appointment){


                $encounter
                    ->appointment
                    ->update([

                        'status'=>'completed'

                    ]);

            }




            return $this->loadEncounterRelations(
                $encounter
            );



        });


    }










    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */


    private function loadEncounterRelations(
        MedicalEncounter $encounter
    ): MedicalEncounter {


        return $encounter->load([

            'patient.user',

            'doctor.user',

            'hospital',

            'appointment',

        ]);

    }



}