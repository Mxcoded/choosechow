<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\ChefSubscriber;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChefSubscriberController extends Controller
{
    use ApiResponse;

    // ==================== CUSTOMER ENDPOINTS ====================

    /**
     * List chefs the current user is subscribed to.
     * GET /api/v1/subscriptions
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $subs = ChefSubscriber::with(['chef.chefProfile'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(min($request->get('per_page', 20), 50));

        return $this->successWithPagination(
            $subs->through(fn($s) => [
                'id' => $s->id,
                'chef_id' => $s->chef_id,
                'user_id' => $s->user_id,
                'notify_new_menu' => $s->notify_new_menu,
                'notify_promotions' => $s->notify_promotions,
                'notify_availability' => $s->notify_availability,
                'created_at' => $s->created_at->toIso8601String(),
                'chef' => $s->chef?->chefProfile ? [
                    'id' => $s->chef->chefProfile->id,
                    'business_name' => $s->chef->chefProfile->business_name,
                    'slug' => $s->chef->chefProfile->slug,
                    'profile_image_url' => $s->chef->chefProfile->profile_image
                        ? asset('storage/' . $s->chef->chefProfile->profile_image)
                        : null,
                    'rating' => (float) ($s->chef->chefProfile->rating ?? 0),
                    'is_online' => $s->chef->chefProfile->is_online,
                    'city' => $s->chef->chefProfile->city,
                ] : null,
            ])
        );
    }

    /**
     * Subscribe to a chef's updates.
     * POST /api/v1/subscriptions/chef/{chef}
     */
    public function subscribe(Request $request, $chefId)
    {
        $user = Auth::user();

        if ($user->id == $chefId) {
            return $this->error('You cannot subscribe to yourself', 400);
        }

        $chef = User::role('chef')->find($chefId);
        if (!$chef || $chef->status !== 'active') {
            return $this->notFound('Chef not found');
        }

        $existing = ChefSubscriber::where('user_id', $user->id)
            ->where('chef_id', $chefId)
            ->first();

        if ($existing) {
            return $this->success([
                'subscribed' => true,
                'subscription' => [
                    'id' => $existing->id,
                    'chef_id' => (int) $chefId,
                    'notify_new_menu' => $existing->notify_new_menu,
                    'notify_promotions' => $existing->notify_promotions,
                    'notify_availability' => $existing->notify_availability,
                ],
            ], 'Already subscribed');
        }

        $sub = ChefSubscriber::create([
            'user_id' => $user->id,
            'chef_id' => $chefId,
            'notify_new_menu' => $request->boolean('notify_new_menu', true),
            'notify_promotions' => $request->boolean('notify_promotions', true),
            'notify_availability' => $request->boolean('notify_availability', true),
        ]);

        return $this->success([
            'subscribed' => true,
            'subscription' => [
                'id' => $sub->id,
                'chef_id' => (int) $chefId,
                'notify_new_menu' => $sub->notify_new_menu,
                'notify_promotions' => $sub->notify_promotions,
                'notify_availability' => $sub->notify_availability,
            ],
        ], 'Subscribed successfully');
    }

    /**
     * Unsubscribe from a chef.
     * DELETE /api/v1/subscriptions/chef/{chef}
     */
    public function unsubscribe($chefId)
    {
        $user = Auth::user();

        ChefSubscriber::where('user_id', $user->id)
            ->where('chef_id', $chefId)
            ->delete();

        return $this->success(['subscribed' => false], 'Unsubscribed successfully');
    }

    /**
     * Check if subscribed to a chef.
     * GET /api/v1/subscriptions/check/{chef}
     */
    public function check($chefId)
    {
        $user = Auth::user();

        $sub = ChefSubscriber::where('user_id', $user->id)
            ->where('chef_id', $chefId)
            ->first();

        return $this->success([
            'subscribed' => $sub !== null,
            'subscription' => $sub ? [
                'id' => $sub->id,
                'chef_id' => (int) $chefId,
                'notify_new_menu' => $sub->notify_new_menu,
                'notify_promotions' => $sub->notify_promotions,
                'notify_availability' => $sub->notify_availability,
            ] : null,
        ]);
    }

    /**
     * Get subscription notification settings.
     * GET /api/v1/subscriptions/settings
     */
    public function settings(Request $request)
    {
        $user = Auth::user();
        $prefs = $user->preferences ?? [];

        return $this->success([
            'notify_new_menu' => $prefs['chef_subscriptions']['notify_new_menu'] ?? true,
            'notify_promotions' => $prefs['chef_subscriptions']['notify_promotions'] ?? true,
            'notify_availability' => $prefs['chef_subscriptions']['notify_availability'] ?? true,
            'email_notifications' => $prefs['chef_subscriptions']['email_notifications'] ?? true,
            'push_notifications' => $prefs['chef_subscriptions']['push_notifications'] ?? true,
        ]);
    }

    /**
     * Update subscription notification settings.
     * PUT /api/v1/subscriptions/settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'notify_new_menu' => 'sometimes|boolean',
            'notify_promotions' => 'sometimes|boolean',
            'notify_availability' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
        ]);

        $prefs = $user->preferences ?? [];
        $prefs['chef_subscriptions'] = array_merge(
            $prefs['chef_subscriptions'] ?? [],
            $validated
        );
        $user->update(['preferences' => $prefs]);

        return $this->success($validated, 'Settings updated');
    }

    // ==================== CHEF/VENDOR ENDPOINTS ====================

    /**
     * Get chef's subscribers list.
     * GET /api/v1/chef/subscribers
     */
    public function subscribers(Request $request)
    {
        $chef = Auth::user();

        $query = ChefSubscriber::with(['user'])
            ->where('chef_id', $chef->id);

        // Search by name or email
        if ($search = $request->get('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subs = $query->orderByDesc('created_at')
            ->paginate(min($request->get('per_page', 20), 50));

        return $this->successWithPagination(
            $subs->through(fn($s) => [
                'id' => $s->id,
                'user_id' => $s->user_id,
                'name' => $s->user?->full_name ?? 'Unknown',
                'email' => $s->user?->email ?? '',
                'avatar_url' => $s->user?->avatar_url,
                'subscribed_at' => $s->created_at->toIso8601String(),
                'notify_new_menu' => $s->notify_new_menu,
                'notify_promotions' => $s->notify_promotions,
            ])
        );
    }

    /**
     * Get subscriber count.
     * GET /api/v1/chef/subscribers/count
     */
    public function subscriberCount()
    {
        $chef = Auth::user();

        $total = ChefSubscriber::where('chef_id', $chef->id)->count();
        $thisMonth = ChefSubscriber::where('chef_id', $chef->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return $this->success([
            'total' => $total,
            'this_month' => $thisMonth,
        ]);
    }

    /**
     * Send notification to all subscribers.
     * POST /api/v1/chef/subscribers/notify
     */
    public function notifySubscribers(Request $request)
    {
        $chef = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:new_menu,promotion,announcement',
            'menu_id' => 'nullable|integer|exists:menus,id',
        ]);

        $subscribers = ChefSubscriber::with(['user'])
            ->where('chef_id', $chef->id)
            ->get();

        $sentCount = 0;
        foreach ($subscribers as $sub) {
            Notification::create([
                'user_id' => $sub->user_id,
                'notifiable_type' => \App\Models\User::class,
                'notifiable_id' => $sub->user_id,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'data' => [
                    'chef_id' => $chef->id,
                    'chef_name' => $chef->chefProfile?->business_name ?? $chef->full_name,
                    'menu_id' => $validated['menu_id'] ?? null,
                ],
                'priority' => 'normal',
            ]);
            $sentCount++;
        }

        return $this->success([
            'sent_count' => $sentCount,
            'total_subscribers' => $subscribers->count(),
        ], 'Notification sent to ' . $sentCount . ' subscriber(s)');
    }
}
