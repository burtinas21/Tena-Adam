<?php

namespace App\Policies;

use App\Models\NotificationTemplate;
use App\Models\User;

class NotificationTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_notifications');
    }

    public function view(User $user, NotificationTemplate $template): bool
    {
        return $user->hasPermission('view_notifications');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('send_notifications');
    }

    public function update(User $user, NotificationTemplate $template): bool
    {
        return $user->hasPermission('send_notifications');
    }

    public function delete(User $user, NotificationTemplate $template): bool
    {
        return $user->hasPermission('send_notifications');
    }
}
