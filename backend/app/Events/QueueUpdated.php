<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Uses ShouldBroadcastNow (not ShouldBroadcast) so the event is
 * broadcast synchronously — no jobs table insert, no serialization issues.
 */
class QueueUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public array $queue;

    public function __construct(object $queueEntry)
    {
        // Store only scalar / string values — never Carbon or Eloquent objects
        $this->queue = [
            'id'                   => (string) $queueEntry->id,
            'doctor_id'            => (string) $queueEntry->doctor_id,
            'hospital_id'          => (string) ($queueEntry->hospital_id ?? ''),
            'queue_number'         => (int)    $queueEntry->queue_number,
            'status'               => (string) $queueEntry->status,
            'queue_date'           => $queueEntry->queue_date
                                        ? (string) $queueEntry->queue_date
                                        : null,
            'called_at'            => $queueEntry->called_at
                                        ? (string) $queueEntry->called_at
                                        : null,
            'started_at'           => $queueEntry->started_at
                                        ? (string) $queueEntry->started_at
                                        : null,
            'ended_at'             => $queueEntry->ended_at
                                        ? (string) $queueEntry->ended_at
                                        : null,
            'walk_in_patient_name' => $queueEntry->walk_in_patient_name ?? null,
            'appointment_id'       => $queueEntry->appointment_id ?? null,
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('queue.' . $this->queue['doctor_id']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.updated';
    }
}
