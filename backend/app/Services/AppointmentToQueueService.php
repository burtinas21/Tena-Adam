<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentToQueueService
{
    protected int $graceMinutes = 15;

    public function generate(string $doctorId, string $date): array
    {
        return DB::transaction(function () use ($doctorId, $date) {

            // Normalise date to YYYY-MM-DD string — avoids Carbon cast issues
            $dateStr = Carbon::parse($date)->toDateString();

            // 1. Prevent duplicate generation — only skip if there are already
            //    ACTIVE (waiting or in_consultation) entries for this doctor/date.
            //    Completed/skipped entries from a previous session do NOT block regeneration.
            $hasActiveEntries = Queue::where('doctor_id', $doctorId)
                ->whereRaw('queue_date = ?', [$dateStr])
                ->whereIn('status', ['waiting', 'in_consultation'])
                ->exists();

            if ($hasActiveEntries) {
                return ['message' => 'Queue already generated for this doctor and date'];
            }

            // 2. Fetch CONFIRMED appointments only for this doctor/date.
            // Pending appointments have not been approved yet and must NOT enter the queue.
            $appointments = Appointment::where('doctor_id', $doctorId)
                ->whereRaw('DATE(scheduled_time) = ?', [$dateStr])
                ->where('status', 'confirmed')
                ->orderBy('scheduled_time')
                ->get();

            // 3. Fetch existing walk-in entries (no appointment_id) that are
            //    still active — never re-queue completed or skipped walk-ins.
            $walkins = Queue::where('doctor_id', $doctorId)
                ->whereRaw('queue_date = ?', [$dateStr])
                ->whereNull('appointment_id')
                ->whereIn('status', ['waiting', 'in_consultation'])
                ->get();

            // 4. Build prioritised list
            $queueItems = [];
            $now = Carbon::now();

            foreach ($appointments as $appointment) {
                $scheduled = Carbon::parse($appointment->scheduled_time);
                $diff      = $scheduled->diffInMinutes($now, false);

                // Urgent appointments always get the highest priority (100)
                if ($appointment->visit_type === 'urgent') {
                    $priority = 100;
                } elseif ($diff < -5) {
                    $priority = 80; // on time or early
                } elseif ($diff <= $this->graceMinutes) {
                    $priority = 60; // slightly late but within grace
                } else {
                    $priority = 10; // very late
                }

                $queueItems[] = [
                    'type'           => 'appointment',
                    'appointment'    => $appointment,
                    'priority'       => $priority,
                    'scheduled_time' => $appointment->scheduled_time,
                ];
            }

            foreach ($walkins as $walkin) {
                $queueItems[] = [
                    'type'           => 'walkin',
                    'queue'          => $walkin,
                    'priority'       => 50,
                    'scheduled_time' => null,
                ];
            }

            // 5. Sort by priority descending (highest first)
            usort($queueItems, fn($a, $b) => $b['priority'] <=> $a['priority']);

            // 6. Create/update queue entries, starting after any existing entries
            //    (e.g. from a previous session that day — completed/skipped entries
            //    are kept; new entries get the next available queue numbers).
            $maxExisting = (int) Queue::where('doctor_id', $doctorId)
                ->whereRaw('queue_date = ?', [$dateStr])
                ->max('queue_number');

            $queueNumber = $maxExisting + 1;

            foreach ($queueItems as $item) {

                if ($item['type'] === 'appointment') {
                    $appt = $item['appointment'];

                    // Skip if this appointment already has a queue entry (any status)
                    $alreadyQueued = Queue::where('appointment_id', $appt->id)
                        ->whereRaw('queue_date = ?', [$dateStr])
                        ->exists();

                    if ($alreadyQueued) {
                        continue;
                    }

                    Queue::create([
                        'appointment_id' => $appt->id,
                        'doctor_id'      => $doctorId,
                        'hospital_id'    => $appt->hospital_id,
                        'queue_date'     => $dateStr,
                        'queue_number'   => $queueNumber++,
                        'priority'       => $item['priority'],
                        'status'         => 'waiting',
                    ]);
                }

                if ($item['type'] === 'walkin') {
                    $item['queue']->update([
                        'queue_number' => $queueNumber++,
                        'status'       => 'waiting',
                    ]);
                }
            }

            return [
                'message' => 'Queue generated successfully',
                'total'   => count($queueItems),
            ];
        });
    }
}
