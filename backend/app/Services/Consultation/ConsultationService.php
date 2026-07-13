<?php

namespace App\Services\Consultation;

use App\Models\Queue;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConsultationService
{
    /**
     * Load consultation information.
     */
    public function openConsultation(string $queueId): Queue
    {
        $queue = Queue::with([
            'appointment.patient.user',
            'appointment.doctor.user',
            'appointment.hospital',
            'appointment.department',
            'appointment.slot',
        ])->find($queueId);

        if (!$queue) {
            throw new ModelNotFoundException(
                'Queue not found.'
            );
        }

        return $queue;
    }
}