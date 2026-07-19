<?php

namespace App\Services;

use App\Events\QueueUpdated;
use App\Models\MedicalEncounter;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class QueueService
{
    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    /** Normalise any date input to "YYYY-MM-DD" string. */
    private function dateString(mixed $date): string
    {
        return Carbon::parse($date)->toDateString();
    }
 private NotificationService $notificationService;

    public function __construct(
        NotificationService $notificationService
    ) {
        $this->notificationService = $notificationService;
    }
    /**
     * Safely fire QueueUpdated — never crashes the caller if broadcasting
     * fails (e.g. SSL issues in local development).
     */
    private function broadcast(Queue $entry): void
    {
        try {
            event(new QueueUpdated($entry));
        } catch (\Throwable $e) {
            Log::warning('[QueueService] QueueUpdated broadcast failed: ' . $e->getMessage());
        }
    }

    /** Next available queue_number for a doctor on a date. */
    private function nextQueueNumber(string $doctorId, string $dateStr): int
    {
        return (int) Queue::where('doctor_id', $doctorId)
            ->whereRaw('queue_date = ?', [$dateStr])
            ->max('queue_number') + 1;
    }

    // -----------------------------------------------------------------------
    // 1. GET doctor queue for a date
    // -----------------------------------------------------------------------

    /**
     * Returns all queue entries for a doctor on a given date,
     * ordered by queue_number, with appointment → patient eager-loaded.
     */
    public function getDoctorQueue(string $doctorId, string $date): Collection
    {
        return Queue::where('doctor_id', $doctorId)
            ->whereRaw('queue_date = ?', [$this->dateString($date)])
            ->orderBy('queue_number')
            ->with(['appointment.patient'])
            ->get([
                'id',
                'queue_number',
                'status',
                'called_at',
                'started_at',
                'ended_at',
                'walk_in_patient_name',
                'appointment_id',
                'doctor_id',
                'hospital_id',
                'queue_date',
                'walk_in_phone',
            ]);
    }

    // -----------------------------------------------------------------------
    // 2. GENERATE a single queue entry (appointment-based or walk-in)
    // -----------------------------------------------------------------------

    public function generate(array $data): Queue
    {
        return DB::transaction(function () use ($data) {

            $dateStr = $this->dateString($data['queue_date']);

            $entry = Queue::create([
                'appointment_id'       => $data['appointment_id'] ?? null,
                'doctor_id'            => $data['doctor_id'],
                'hospital_id'          => $data['hospital_id'],
                'queue_date'           => $dateStr,
                'queue_number'         => $this->nextQueueNumber($data['doctor_id'], $dateStr),
                'status'               => 'waiting',
                'walk_in_patient_name' => $data['walk_in_patient_name'] ?? null,
                'walk_in_phone'        => $data['walk_in_phone'] ?? null,
            ]);
if ($entry->appointment) {

    $this->notificationService
        ->sendQueueNotification(
            $entry
        );

}
            return $entry;
        });
    }

    // -----------------------------------------------------------------------
    // 3. CALL NEXT waiting patient
    // -----------------------------------------------------------------------

    /**
     * Finds the lowest queue_number with status=waiting for the given
     * doctor/date, moves it to in_consultation.
     * Sets called_at and started_at — never touches ended_at.
     */
    public function callNextPatient(string $doctorId, string $date): array
    {
        $found = null;

        DB::transaction(function () use ($doctorId, $date, &$found) {

            $dateStr = $this->dateString($date);

            // Guard: only one active consultation at a time per doctor per day
            $alreadyInConsultation = Queue::where('doctor_id', $doctorId)
                ->whereRaw('queue_date = ?', [$dateStr])
                ->where('status', 'in_consultation')
                ->lockForUpdate()
                ->exists();

            if ($alreadyInConsultation) {
                return; // $found stays null — handled below
            }

            $found = Queue::where('doctor_id', $doctorId)
                ->whereRaw('queue_date = ?', [$dateStr])
                ->where('status', 'waiting')
                ->orderBy('queue_number', 'asc')
                ->lockForUpdate()
                ->first();

            if ($found) {
                $found->update([
                    'status'     => 'in_consultation',
                    'called_at'  => now(),
                    'started_at' => now(),
                    // ended_at is intentionally NOT set here
                ]);

                // ── Auto-create Medical Encounter ──────────────────────────
                // If this queue entry is linked to an appointment, create the
                // encounter immediately so the doctor sees the patient in EMR.
                if ($found->appointment_id) {
                    $appt = \App\Models\Appointment::find($found->appointment_id);

                    if ($appt && !MedicalEncounter::where('appointment_id', $appt->id)->exists()) {
                        MedicalEncounter::create([
                            'patient_id'    => $appt->patient_id,
                            'doctor_id'     => $appt->doctor_id,
                            'hospital_id'   => $appt->hospital_id,
                            'appointment_id'=> $appt->id,
                            'encounter_date'=> now(),
                            'status'        => 'in_progress',
                        ]);
                    }
                }
            }
        });

        // Doctor already has an active consultation
        $activeConsultation = Queue::where('doctor_id', $doctorId)
            ->whereRaw('queue_date = ?', [$this->dateString($date)])
            ->where('status', 'in_consultation')
            ->first();

        if ($activeConsultation && ! $found) {
            return [
                'message'             => 'A patient is already in consultation. Complete or skip the current consultation before calling the next patient.',
                'active_consultation' => [
                    'id'           => $activeConsultation->id,
                    'queue_number' => $activeConsultation->queue_number,
                    'status'       => $activeConsultation->status,
                ],
            ];
        }

        if (! $found) {
            return ['message' => 'No patients waiting in queue'];
        }

        $this->broadcast($found->fresh());
        $this->notificationService
    ->sendQueueNotification(
        $found->fresh()
    );

        return [
            'message'         => 'Patient called successfully',
            'current_patient' => [
                'id'                   => $found->id,
                'queue_number'         => $found->queue_number,
                'status'               => $found->status,
                'doctor_id'            => $found->doctor_id,
                'called_at'            => $found->called_at ? (string) $found->called_at : null,
                'started_at'           => $found->started_at ? (string) $found->started_at : null,
                'ended_at'             => null,
                'walk_in_patient_name' => $found->walk_in_patient_name,
                'appointment_id'       => $found->appointment_id,
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // 4. COMPLETE consultation  (alias: completeConsultation)
    // -----------------------------------------------------------------------

    /**
     * Marks the queue entry as completed and records ended_at.
     * This is the ONLY method that sets ended_at.
     */
    public function completeConsultation(string $queueId): array
    {
        $entry = null;

        DB::transaction(function () use ($queueId, &$entry) {
            $entry = Queue::findOrFail($queueId);
            $entry->update([
                'status'   => 'completed',
                'ended_at' => now(),
            ]);
        });

        $this->broadcast($entry->fresh());
        $this->notificationService
    ->sendQueueNotification(
        $entry->fresh()
    );

        return [
            'message' => 'Consultation completed successfully',
            'queue'   => [
                'id'           => $entry->id,
                'queue_number' => $entry->queue_number,
                'status'       => $entry->status,
                'started_at'   => $entry->started_at ? (string) $entry->started_at : null,
                'ended_at'     => $entry->ended_at ? (string) $entry->ended_at : null,
            ],
        ];
    }

    /**
     * Alias for backward compatibility.
     */
    public function completePatient(string $queueId): array
    {
        return $this->completeConsultation($queueId);
    }

    // -----------------------------------------------------------------------
    // 5. SKIP patient — move to end of queue
    // -----------------------------------------------------------------------

    /**
     * Marks the entry as skipped (must be waiting or in_consultation).
     * Creates a NEW queue entry at the end with status=waiting.
     * Broadcasts both changes.
     */
    public function skipPatient(string $queueId): array
    {
        $original = null;
        $newEntry  = null;

        DB::transaction(function () use ($queueId, &$original, &$newEntry) {

            $original = Queue::findOrFail($queueId);

            if (! in_array($original->status, ['waiting', 'in_consultation'])) {
                throw new \InvalidArgumentException(
                    "Only waiting or in_consultation patients can be skipped."
                );
            }

            $original->update(['status' => 'skipped']);

            $dateStr = $this->dateString($original->queue_date);

            $newEntry = Queue::create([
                'appointment_id'       => $original->appointment_id,
                'doctor_id'            => $original->doctor_id,
                'hospital_id'          => $original->hospital_id,
                'queue_date'           => $dateStr,
                'queue_number'         => $this->nextQueueNumber($original->doctor_id, $dateStr),
                'status'               => 'waiting',
                'walk_in_patient_name' => $original->walk_in_patient_name,
                'walk_in_phone'        => $original->walk_in_phone,
            ]);
        });

        $this->broadcast($original->fresh());
        $this->broadcast($newEntry->fresh());
$this->notificationService
    ->sendQueueNotification(
        $newEntry->fresh()
    );
        return [
            'message'   => 'Patient skipped and moved to end of queue',
            'original'  => [
                'id'           => $original->id,
                'queue_number' => $original->queue_number,
                'status'       => $original->status,
            ],
            'new_entry' => [
                'id'           => $newEntry->id,
                'queue_number' => $newEntry->queue_number,
                'status'       => $newEntry->status,
            ],
        ];
    }

    // -----------------------------------------------------------------------
    // 6. RECALL skipped patient
    // -----------------------------------------------------------------------

    /**
     * Puts a skipped patient back to waiting status.
     * Does NOT change queue_number — they return at their original position
     * in the ordering (lowest waiting number gets called next).
     */
    public function recallPatient(string $queueId): array
    {
        $entry = Queue::findOrFail($queueId);

        if ($entry->status !== 'skipped') {
            return ['message' => 'Only skipped patients can be recalled'];
        }

        DB::transaction(function () use ($entry) {
            $entry->update([
                'status'    => 'waiting',
                'called_at' => null,
            ]);
        });

        $this->broadcast($entry->fresh());
        $this->notificationService
    ->sendQueueNotification(
        $entry->fresh()
    );

        return [
            'message' => 'Patient recalled to waiting',
            'queue'   => [
                'id'           => $entry->id,
                'queue_number' => $entry->queue_number,
                'status'       => $entry->status,
            ],
        ];
    }
}
