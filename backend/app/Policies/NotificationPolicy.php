<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    /**
     * View any notifications (index).
     */
    public function viewAny(User $user): bool
    {
        return true; // Every authenticated user can list their own notifications
    }

    /**
     * View a notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id
            || $user->hasAnyRole(['platform_admin', 'hospital_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['platform_admin', 'hospital_admin']);
    }

    public function update(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id;
    }

    public function delete(User $user, Notification $notification): bool
    {
        return $user->id === $notification->user_id
            || $user->hasRole('platform_admin');
    }
}