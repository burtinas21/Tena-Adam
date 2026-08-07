<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Queue;

class QueuePolicy
{
    public function generateQueue(User $user): bool
    {
        return $user->hasPermission('manage_queue');
    }

    public function callNext(User $user, Queue $queue = null): bool
    {
        return $user->hasPermission('call_next_patient')
            || $user->hasPermission('manage_queue');
    }

    public function complete(User $user, Queue $queue): bool
    {
        return $user->hasPermission('manage_queue');
    }

    public function skip(User $user, Queue $queue): bool
    {
        return $user->hasPermission('manage_queue');
    }

    public function recall(User $user, Queue $queue): bool
    {
        return $user->hasPermission('manage_queue');
    }

    public function view(User $user, Queue $queue = null): bool
    {
        return $user->hasPermission('view_queue')
            || $user->hasPermission('manage_queue');
    }
}
