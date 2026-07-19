<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Notification\StoreNotificationRequest;
use App\Http\Requests\Api\Notification\UpdateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\UserNotificationPreference;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * List current user's notifications.
     */
    public function index(Request $request)
    {
        // No explicit authorize needed — query is scoped to the authenticated user
        $notifications = $this->notificationService
            ->getUserNotifications(
                $request->user()->id,
                $request->boolean('unread')
            );

        return NotificationResource::collection($notifications);
    }

    /**
     * Unread count for bell badge.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'sent', 'failed'])
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark ALL unread notifications as read for the current user.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('status', '!=', 'read')
            ->update(['status' => 'read']);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    /**
     * Create a notification (admin only).
     */
    public function store(StoreNotificationRequest $request): NotificationResource
    {
        $this->authorize('create', Notification::class);

        $notification = $this->notificationService->createNotification($request->validated());

        return new NotificationResource($notification);
    }

    /**
     * Show a single notification.
     */
    public function show(Notification $notification): NotificationResource
    {
        $this->authorize('view', $notification);

        return new NotificationResource($notification->load('user'));
    }

    /**
     * Mark a single notification as read.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification): NotificationResource
    {
        $this->authorize('update', $notification);

        $notification = $this->notificationService->markAsRead($notification->id);

        return new NotificationResource($notification);
    }

    /**
     * Retry a failed notification.
     */
    public function retry(Notification $notification): NotificationResource
    {
        $this->authorize('update', $notification);

        $notification = $this->notificationService->retryFailedNotification($notification->id);

        return new NotificationResource($notification);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $this->authorize('delete', $notification);

        $this->notificationService->deleteNotification($notification->id);

        return response()->json(['message' => 'Notification deleted successfully.']);
    }

    // ── User Notification Preferences ────────────────────────────────────

    /**
     * Get current user's notification preferences.
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $prefs = UserNotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'email_enabled'        => true,
                'sms_enabled'          => true,
                'push_enabled'         => true,
                'appointment_reminders' => true,
                'queue_updates'        => true,
                'promotional'          => false,
            ]
        );

        return response()->json($prefs);
    }

    /**
     * Update current user's notification preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email_enabled'         => 'boolean',
            'sms_enabled'           => 'boolean',
            'push_enabled'          => 'boolean',
            'appointment_reminders' => 'boolean',
            'queue_updates'         => 'boolean',
            'promotional'           => 'boolean',
        ]);

        $prefs = UserNotificationPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return response()->json($prefs);
    }
}
