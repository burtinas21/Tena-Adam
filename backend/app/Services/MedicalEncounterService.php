<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\MedicalEncounter;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\NotificationService;

class MedicalEncounterService

{
    public function __construct(
    private NotificationService $notificationService
) {
}
    /**
 * Start a new medical encounter.
 *
 * Creates the encounter when the doctor starts the consultation.
 *
 * @throws ValidationException
 */
public function createEncounter(array $data): MedicalEncounter
{
    return DB::transaction(function () use ($data) {

        /*
        |--------------------------------------------------------------------------
        | Load Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = Appointment::with('queue')
            ->findOrFail($data['appointment_id']);

        /*
        |--------------------------------------------------------------------------
        | Appointment must be approved
        |--------------------------------------------------------------------------
        */

        if (!in_array($appointment->status, ['confirmed', 'approved'])) {

            throw ValidationException::withMessages([
                'appointment' => [
                    'Only confirmed appointments can start a consultation.'
                ]
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Encounter
        |--------------------------------------------------------------------------
        */

        if (
            MedicalEncounter::where(
                'appointment_id',
                $appointment->id
            )->exists()
        ) {

            throw ValidationException::withMessages([
                'appointment' => [
                    'This appointment already has a medical encounter.'
                ]
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Doctor
        |--------------------------------------------------------------------------
        */

        if (
            $appointment->doctor_id !== $data['doctor_id']
        ) {

            throw ValidationException::withMessages([
                'doctor' => [
                    'You are not assigned to this appointment.'
                ]
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Patient
        |--------------------------------------------------------------------------
        */

        if (
            $appointment->patient_id !== $data['patient_id']
        ) {

            throw ValidationException::withMessages([
                'patient' => [
                    'Patient does not belong to this appointment.'
                ]
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Queue must exist
        |--------------------------------------------------------------------------
        */

        $queue = $appointment->queue;

        if (!$queue) {

            throw ValidationException::withMessages([
                'queue' => [
                    'Queue record not found.'
                ]
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Queue must be in consultation
        |--------------------------------------------------------------------------
        */

        if ($queue->status !== 'in_consultation') {

            throw ValidationException::withMessages([
                'queue' => [
                    'Patient is not currently in consultation.'
                ]
            ]);
        }
        $encounter = MedicalEncounter::create([

            'patient_id' => $appointment->patient_id,

            'doctor_id' => $appointment->doctor_id,

            'hospital_id' => $appointment->hospital_id,

            'appointment_id' => $appointment->id,

            'encounter_date' => now(),
            'chief_complaint' => null,

            'history' => null,

            'physical_exam' => null,

            'assessment' => null,

            'diagnosis' => null,

            'diagnosis_icd10' => null,

            'treatment_plan' => null,

            'clinical_notes' => null,

            'follow_up_date' => null,

            'status' => 'in_progress',

        ]);

        // Mark queue started_at
        $queue->update(['started_at' => now()]);

        $encounter = $this->loadRelations($encounter);

        $this->notificationService->sendMedicalEncounterNotification(
            $encounter,
            'started'
        );

        return $encounter;
    });
}
public function findEncounter(string $encounterId): MedicalEncounter
{
    try {
        return $this->loadRelations(
            MedicalEncounter::findOrFail($encounterId)
        );
    } catch (ModelNotFoundException $e) {
        throw ValidationException::withMessages([
            'encounter' => ['Medical encounter not found.'],
        ]);
    }
}

private function findEncounterOrFail(string $encounterId): MedicalEncounter
{
    return MedicalEncounter::findOrFail($encounterId);
}

/**
 * Update an existing medical encounter.
 *
 * @throws ValidationException
 */
public function updateEncounter(
    string $encounterId,
    array $data
): MedicalEncounter
{
    try {

        return DB::transaction(function () use (
            $encounterId,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Find Encounter
            |--------------------------------------------------------------------------
            */

            $encounter = $this->findEncounterOrFail(
                $encounterId
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent Editing Completed Encounter
            |--------------------------------------------------------------------------
            */

            $this->ensureEditable(
                $encounter
            );

            /*
            |--------------------------------------------------------------------------
            | Update Medical Information
            |--------------------------------------------------------------------------
            */

            $encounter->fill([

                'chief_complaint' =>
                    $data['chief_complaint']
                        ?? $encounter->chief_complaint,

                'history' =>
                    $data['history']
                        ?? $encounter->history,

                'physical_exam' =>
                    $data['physical_exam']
                        ?? $encounter->physical_exam,

                'assessment' =>
                    $data['assessment']
                        ?? $encounter->assessment,

                'diagnosis' =>
                    $data['diagnosis']
                        ?? $encounter->diagnosis,

                'diagnosis_icd10' =>
                    $data['diagnosis_icd10']
                        ?? $encounter->diagnosis_icd10,

                'treatment_plan' =>
                    $data['treatment_plan']
                        ?? $encounter->treatment_plan,

                'clinical_notes' =>
                    $data['clinical_notes']
                        ?? $encounter->clinical_notes,

                'follow_up_date' =>
                    $data['follow_up_date']
                        ?? $encounter->follow_up_date,

            ]);

            $encounter->save();

            return $this->loadRelations(
                $encounter->fresh()
            );

        });

    } catch (ModelNotFoundException $e) {

        throw ValidationException::withMessages([
            'encounter' => [
                'Medical encounter not found.'
            ]
        ]);

    }
}
private function ensureEditable(
    MedicalEncounter $encounter
): void {

    if ($encounter->status === 'completed') {

        throw ValidationException::withMessages([
            'encounter' => [
                'Completed medical encounters cannot be modified.'
            ]
        ]);

    }

}
private function loadRelations(
    MedicalEncounter $encounter
): MedicalEncounter {

    return $encounter->load([
        'patient.user',
        'doctor.user',
        'hospital',
        'appointment',
        'vital',
    ]);
}
public function completeEncounter(string $encounterId): MedicalEncounter
{
    try {

        return DB::transaction(function () use ($encounterId) {
            $encounter = $this->findEncounterOrFail(
                $encounterId
            );

            $this->ensureEditable(
                $encounter
            );

            if (empty(trim((string) $encounter->chief_complaint))) {

                throw ValidationException::withMessages([
                    'chief_complaint' => [
                        'Chief complaint is required before completing the consultation.'
                    ]
                ]);

            }

            if (empty(trim((string) $encounter->assessment))) {

                throw ValidationException::withMessages([
                    'assessment' => [
                        'Assessment is required before completing the consultation.'
                    ]
                ]);

            }

            if (empty(trim((string) $encounter->diagnosis))) {

                throw ValidationException::withMessages([
                    'diagnosis' => [
                        'Diagnosis is required before completing the consultation.'
                    ]
                ]);

            }
            $encounter->load('appointment.queue');
            $appointment = $encounter->appointment;

            // Walk-in encounters have no appointment — complete queue via doctor_id + date
            if (!$appointment) {
                $encounter->update(['status' => 'completed']);

                // Find the in_consultation queue entry for this doctor today
                $queue = Queue::where('doctor_id', $encounter->doctor_id)
                    ->where('status', 'in_consultation')
                    ->whereDate('started_at', now()->toDateString())
                    ->where(function ($q) use ($encounter) {
                        if ($encounter->walk_in_patient_name) {
                            $q->where('walk_in_patient_name', $encounter->walk_in_patient_name);
                        }
                    })
                    ->first();

                if ($queue) {
                    $queue->update(['status' => 'completed', 'ended_at' => now()]);
                }

                $encounter = $this->loadRelations($encounter->fresh());
                $this->notificationService->sendMedicalEncounterNotification($encounter, 'completed');
                return $encounter;
            }

            $queue = $appointment->queue;

            if (!$queue) {

                throw ValidationException::withMessages([
                    'queue' => [
                        'Queue record not found.'
                    ]
                ]);

            }
            $encounter->update([

                'status' => 'completed',

            ]);
            $queue->update([

                'status'   => 'completed',

                'ended_at' => now(),

            ]);
            $appointment->update([

                'status' => 'completed',

            ]);
           $encounter = $this->loadRelations(
    $encounter->fresh()
);

$this->notificationService->sendMedicalEncounterNotification(
    $encounter,
    'completed'
);

return $encounter;

        });

    } catch (ModelNotFoundException $e) {

        throw ValidationException::withMessages([
            'encounter' => [
                'Medical encounter not found.'
            ]
        ]);

    }
}
}