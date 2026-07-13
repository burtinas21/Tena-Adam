<?php

namespace App\Policies;

use App\Models\Queue;
use App\Models\User;

class ConsultationPolicy
{
    /**
     * Determine whether the doctor can open the consultation.
     */
    public function open(User $user, Queue $queue): bool
    {
        // Must be a doctor
        if (!$user->hasRole('doctor')) {
            return false;
        }

        // Queue must belong to this doctor
        if ($queue->doctor_id !== $user->id) {
            return false;
        }

        // Queue must be in consultation
        if ($queue->status !== 'in_consultation') {
            return false;
        }

        return true;
    }
}