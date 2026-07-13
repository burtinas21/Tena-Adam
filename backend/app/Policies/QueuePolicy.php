<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Queue;

class QueuePolicy
{
        public function generateQueue(User $user): bool
    {
        return $user->hasAnyRole([
            'hospital_admin',
            'platform_admin',
            'receptionist'
        ]);
    }
        public function callNext(User $user, Queue $queue = null): bool
    {
        return $user->hasRole('doctor')
            || $user->hasAnyRole(['hospital_admin', 'platform_admin']);
    }
        public function complete(User $user, Queue $queue): bool
    {
        return $user->hasRole('doctor')
            || $user->hasAnyRole(['hospital_admin', 'platform_admin']);
    }
        public function skip(User $user, Queue $queue): bool
    {
        return $user->hasRole('doctor')
            || $user->hasAnyRole(['hospital_admin', 'platform_admin']);
    }
        public function recall(User $user, Queue $queue): bool
    {
        return $user->hasRole('doctor')
            || $user->hasAnyRole(['hospital_admin', 'platform_admin']);
    }
        public function view(User $user, Queue $queue = null): bool
    {
        return $user->hasAnyRole([
            'doctor',
            'hospital_admin',
            'platform_admin',
            'receptionist'
        ]);
    }
}