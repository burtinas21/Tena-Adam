<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    /**
     * View any notifications (index).
     * Every authenticated user can list their own notifications.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_notifications');
    }

    /**
     * View a single notification.
     */
    public function view(User $user, Notification $notification): bool
    {
        if (!$user->hasPermission('view_notifications')) {
            return false;
        }

        return $user->id === $notification->user_id
            || $user->hasAnyRole(['platform_admin', 'hospital_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('send_notifications');
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
