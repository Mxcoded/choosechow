<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get user's notifications.
     * 
     * GET /api/v1/notifications
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        // Query by user_id OR by notifiable (polymorphic)
        $query = Notification::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere(function ($q2) use ($userId) {
                  $q2->where('notifiable_type', \App\Models\User::class)
                     ->where('notifiable_id', $userId);
              });
        });

        // Filter by read status
        if ($request->has('unread_only') && $request->boolean('unread_only')) {
            $query->whereNull('read_at');
        }

        $perPage = min($request->get('per_page', 20), 50);
        $notifications = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->successWithPagination(
            $notifications->through(fn($notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'data' => $notification->data,
                'read_at' => $notification->read_at?->toIso8601String(),
                'is_read' => $notification->read_at !== null,
                'created_at' => $notification->created_at?->toIso8601String(),
                'created_at_human' => $notification->created_at?->diffForHumans(),
            ])
        );
    }

    /**
     * Get unread notifications count.
     * 
     * GET /api/v1/notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;
        
        $count = Notification::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere(function ($q2) use ($userId) {
                  $q2->where('notifiable_type', \App\Models\User::class)
                     ->where('notifiable_id', $userId);
              });
        })->whereNull('read_at')->count();

        return $this->success([
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark a notification as read.
     * 
     * POST /api/v1/notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = $request->user()->id;
        
        $notification = Notification::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere(function ($q2) use ($userId) {
                  $q2->where('notifiable_type', \App\Models\User::class)
                     ->where('notifiable_id', $userId);
              });
        })->findOrFail($id);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return $this->success(null, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     * 
     * POST /api/v1/notifications/read-all
     */
    public function markAllAsRead(Request $request)
    {
        $userId = $request->user()->id;
        
        Notification::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere(function ($q2) use ($userId) {
                  $q2->where('notifiable_type', \App\Models\User::class)
                     ->where('notifiable_id', $userId);
              });
        })->whereNull('read_at')->update(['read_at' => now()]);

        return $this->success(null, 'All notifications marked as read');
    }

    /**
     * Get notification settings.
     * 
     * GET /api/v1/notifications/settings
     */
    public function settings(Request $request)
    {
        $user = $request->user();
        $preferences = $user->preferences ?? [];
        
        // Default notification settings
        $defaultSettings = [
            'push_enabled' => true,
            'email_enabled' => true,
            'order_updates' => true,
            'promotions' => true,
            'new_menu_items' => true,
            'chat_messages' => true,
            'review_reminders' => true,
        ];

        $notificationSettings = array_merge(
            $defaultSettings,
            $preferences['notifications'] ?? []
        );

        return $this->success([
            'settings' => $notificationSettings,
        ]);
    }

    /**
     * Update notification settings.
     * 
     * PUT /api/v1/notifications/settings
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();

        $validatedSettings = $request->validate([
            'push_enabled' => 'sometimes|boolean',
            'email_enabled' => 'sometimes|boolean',
            'order_updates' => 'sometimes|boolean',
            'promotions' => 'sometimes|boolean',
            'new_menu_items' => 'sometimes|boolean',
            'chat_messages' => 'sometimes|boolean',
            'review_reminders' => 'sometimes|boolean',
        ]);

        $preferences = $user->preferences ?? [];
        $preferences['notifications'] = array_merge(
            $preferences['notifications'] ?? [],
            $validatedSettings
        );

        $user->update(['preferences' => $preferences]);

        return $this->success([
            'settings' => $preferences['notifications'],
        ], 'Notification settings updated');
    }

    /**
     * Delete a notification.
     * 
     * DELETE /api/v1/notifications/{id}
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;
        
        $notification = Notification::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere(function ($q2) use ($userId) {
                  $q2->where('notifiable_type', \App\Models\User::class)
                     ->where('notifiable_id', $userId);
              });
        })->findOrFail($id);

        $notification->delete();

        return $this->success(null, 'Notification deleted');
    }
}
