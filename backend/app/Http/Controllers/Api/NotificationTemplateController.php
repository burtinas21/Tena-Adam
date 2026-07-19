<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    /**
     * List all notification templates.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', NotificationTemplate::class);

        return response()->json(
            NotificationTemplate::orderBy('name')->get()
        );
    }

    /**
     * Show a single template.
     */
    public function show(NotificationTemplate $notificationTemplate): JsonResponse
    {
        $this->authorize('view', $notificationTemplate);

        return response()->json($notificationTemplate);
    }

    /**
     * Create a template.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', NotificationTemplate::class);

        $data = $request->validate([
            'name'       => 'required|string|max:100|unique:notification_templates,name',
            'subject'    => 'nullable|string|max:255',
            'email_body' => 'nullable|string',
            'sms_body'   => 'nullable|string',
            'push_body'  => 'nullable|string',
            'variables'  => 'nullable|array',
            'is_active'  => 'boolean',
        ]);

        $template = NotificationTemplate::create($data);

        return response()->json($template, 201);
    }

    /**
     * Update a template.
     */
    public function update(Request $request, NotificationTemplate $notificationTemplate): JsonResponse
    {
        $this->authorize('update', $notificationTemplate);

        $data = $request->validate([
            'name'       => 'sometimes|string|max:100|unique:notification_templates,name,' . $notificationTemplate->id,
            'subject'    => 'nullable|string|max:255',
            'email_body' => 'nullable|string',
            'sms_body'   => 'nullable|string',
            'push_body'  => 'nullable|string',
            'variables'  => 'nullable|array',
            'is_active'  => 'boolean',
        ]);

        $notificationTemplate->update($data);

        return response()->json($notificationTemplate->fresh());
    }

    /**
     * Delete a template.
     */
    public function destroy(NotificationTemplate $notificationTemplate): JsonResponse
    {
        $this->authorize('delete', $notificationTemplate);

        $notificationTemplate->delete();

        return response()->json(['message' => 'Template deleted successfully.']);
    }
}
