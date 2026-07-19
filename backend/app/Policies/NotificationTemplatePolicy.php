<?php

namespace App\Policies;

use App\Models\NotificationTemplate;
use App\Models\User;

class NotificationTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['platform_admin', 'hospital_admin']);
    }

    public function view(User $user, NotificationTemplate $template): bool
    {
        return $user->hasAnyRole(['platform_admin', 'hospital_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('platform_admin');
    }

    public function update(User $user, NotificationTemplate $template): bool
    {
        return $user->hasRole('platform_admin');
    }

    public function delete(User $user, NotificationTemplate $template): bool
    {
        return $user->hasRole('platform_admin');
    }
}
