<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserNotificationPreference;

class UserNotificationPreferencePolicy
{
    /**
     * View notification preferences.
     */
    public function view(
        User $user,
        UserNotificationPreference $preference
    ): bool {
        return $user->id === $preference->user_id
            || $user->hasRole('platform_admin');
    }

    /**
     * Update notification preferences.
     */
    public function update(
        User $user,
        UserNotificationPreference $preference
    ): bool {
        return $user->id === $preference->user_id;
    }
}