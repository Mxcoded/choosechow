<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ChefProfile;
use App\Models\ChefSubscriber;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChefDashboardController extends Controller
{
    /**
     * Get vendor dashboard data
     * GET /api/v1/chef/dashboard
     */
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        // Today's stats
        $todayStart = Carbon::today();
        $todayOrders = Order::where('chef_id', $user->id)
            ->whereDate('created_at', $todayStart)
            ->get();

        // Weekly stats
        $weekStart = Carbon::now()->startOfWeek();
        $weeklyOrders = Order::where('chef_id', $user->id)
            ->where('created_at', '>=', $weekStart)
            ->get();

        // Monthly stats
        $monthStart = Carbon::now()->startOfMonth();
        $monthlyOrders = Order::where('chef_id', $user->id)
            ->where('created_at', '>=', $monthStart)
            ->get();

        // Calculate stats
        $stats = [
            'today_orders' => $todayOrders->count(),
            'today_earnings' => $todayOrders->where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('chef_id', $user->id)
                ->whereIn('status', ['pending', 'preparing'])
                ->count(),
            'completed_orders' => Order::where('chef_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'weekly_orders' => $weeklyOrders->count(),
            'weekly_earnings' => $weeklyOrders->where('payment_status', 'paid')->sum('total_amount'),
            'monthly_orders' => $monthlyOrders->count(),
            'monthly_earnings' => $monthlyOrders->where('payment_status', 'paid')->sum('total_amount'),
            'total_orders' => $chefProfile?->total_orders ?? Order::where('chef_id', $user->id)->count(),
            'total_earnings' => Order::where('chef_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'rating' => (float) ($chefProfile?->rating ?? $user->average_rating ?? 0),
            'total_reviews' => $chefProfile?->total_reviews ?? $user->review_count ?? 0,
            'menu_items' => Menu::where('user_id', $user->id)->count(),
            'subscribers' => ChefSubscriber::where('chef_id', $user->id)->count(),
            'is_online' => $chefProfile?->is_online ?? false,
        ];

        // Recent orders
        $recentOrders = Order::with(['user', 'items.menu'])
            ->where('chef_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($order) => $this->formatOrder($order));

        // Pending orders that need attention
        $pendingOrders = Order::with(['user', 'items.menu'])
            ->where('chef_id', $user->id)
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get()
            ->map(fn($order) => $this->formatOrder($order));

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_orders' => $recentOrders,
                'pending_orders' => $pendingOrders,
                'profile' => $chefProfile ? $this->formatProfile($chefProfile) : null,
            ],
        ]);
    }

    /**
     * Get chef's orders
     * GET /api/v1/chef/orders
     */
    public function orders(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Order::with(['user', 'items.menu'])
            ->where('chef_id', $user->id);

        // Filter by status
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->whereIn('status', ['pending', 'preparing', 'ready']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by date
        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        // Filter by date range
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Search by order number or customer name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->input('per_page', 15);
        $orders = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => collect($orders->items())->map(fn($o) => $this->formatOrder($o)),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Get single order detail
     * GET /api/v1/chef/orders/{id}
     */
    public function showOrder($id): JsonResponse
    {
        $user = Auth::user();
        $order = Order::with(['user', 'items.menu'])
            ->where('chef_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatOrder($order, true),
        ]);
    }

    /**
     * Update order status
     * PUT /api/v1/chef/orders/{id}/status
     */
    public function updateOrderStatus(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $order = Order::where('chef_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        // Validate status transition
        $validTransitions = [
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        $currentStatus = $order->status;
        $newStatus = $validated['status'];

        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'",
            ], 422);
        }

        $order->update(['status' => $newStatus]);

        // Notify customer about status change
        $statusLabels = [
            'pending' => 'Order Received',
            'preparing' => 'Preparing Your Order',
            'ready' => 'Ready for Pickup/Delivery',
            'completed' => 'Order Completed',
            'cancelled' => 'Order Cancelled',
        ];
        $statusLabel = $statusLabels[$newStatus] ?? 'Order Status Updated';
        Notification::create([
            'user_id' => $order->user_id,
            'notifiable_type' => User::class,
            'notifiable_id' => $order->user_id,
            'type' => 'order_status',
            'title' => $statusLabel,
            'message' => "Order #{$order->order_number}: {$statusLabel}",
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'previous_status' => $currentStatus,
                'new_status' => $newStatus,
            ],
            'priority' => 'high',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'data' => $this->formatOrder($order->fresh(['user', 'items.menu'])),
        ]);
    }

    /**
     * Get chef's menu items
     * GET /api/v1/chef/menus
     */
    public function menus(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Menu::with(['cuisines', 'dietaryPreferences'])
            ->where('user_id', $user->id);

        // Filter by availability
        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $menus = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => collect($menus->items())->map(fn($m) => $this->formatMenu($m)),
            'meta' => [
                'current_page' => $menus->currentPage(),
                'last_page' => $menus->lastPage(),
                'per_page' => $menus->perPage(),
                'total' => $menus->total(),
            ],
        ]);
    }

    /**
     * Create a new menu item
     * POST /api/v1/chef/menus
     */
    public function createMenu(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'preparation_time' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:5120', // 5MB max
            'cuisine_ids' => 'nullable|array',
            'cuisine_ids.*' => 'exists:cuisines,id',
            'dietary_preference_ids' => 'nullable|array',
            'dietary_preference_ids.*' => 'exists:dietary_preferences,id',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'] . '-' . uniqid()),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category' => $validated['category'] ?? null,
            'preparation_time' => $validated['preparation_time'] ?? null,
            'is_available' => $validated['is_available'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'image' => $imagePath,
        ]);

        // Attach cuisines
        if (!empty($validated['cuisine_ids'])) {
            $menu->cuisines()->attach($validated['cuisine_ids']);
        }

        // Attach dietary preferences
        if (!empty($validated['dietary_preference_ids'])) {
            $menu->dietaryPreferences()->attach($validated['dietary_preference_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully',
            'data' => $this->formatMenu($menu->fresh(['cuisines', 'dietaryPreferences'])),
        ], 201);
    }

    /**
     * Update a menu item
     * PUT /api/v1/chef/menus/{id}
     */
    public function updateMenu(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $menu = Menu::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'preparation_time' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:5120',
            'cuisine_ids' => 'nullable|array',
            'cuisine_ids.*' => 'exists:cuisines,id',
            'dietary_preference_ids' => 'nullable|array',
            'dietary_preference_ids.*' => 'exists:dietary_preferences,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        // Update slug if name changed
        if (isset($validated['name']) && $validated['name'] !== $menu->name) {
            $validated['slug'] = Str::slug($validated['name'] . '-' . uniqid());
        }

        $menu->update($validated);

        // Sync cuisines
        if (isset($validated['cuisine_ids'])) {
            $menu->cuisines()->sync($validated['cuisine_ids']);
        }

        // Sync dietary preferences
        if (isset($validated['dietary_preference_ids'])) {
            $menu->dietaryPreferences()->sync($validated['dietary_preference_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully',
            'data' => $this->formatMenu($menu->fresh(['cuisines', 'dietaryPreferences'])),
        ]);
    }

    /**
     * Delete a menu item
     * DELETE /api/v1/chef/menus/{id}
     */
    public function deleteMenu($id): JsonResponse
    {
        $user = Auth::user();
        $menu = Menu::where('user_id', $user->id)->findOrFail($id);

        // Delete image
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully',
        ]);
    }

    /**
     * Toggle menu item availability
     * POST /api/v1/chef/menus/{id}/toggle-availability
     */
    public function toggleMenuAvailability($id): JsonResponse
    {
        $user = Auth::user();
        $menu = Menu::where('user_id', $user->id)->findOrFail($id);

        $menu->update(['is_available' => !$menu->is_available]);

        return response()->json([
            'success' => true,
            'message' => $menu->is_available ? 'Menu item is now available' : 'Menu item is now unavailable',
            'data' => $this->formatMenu($menu),
        ]);
    }

    /**
     * Get earnings summary
     * GET /api/v1/chef/earnings
     */
    public function earnings(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Get period filter
        $period = $request->input('period', 'month'); // day, week, month, year, all

        $query = Order::where('chef_id', $user->id)
            ->where('payment_status', 'paid');

        switch ($period) {
            case 'day':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', Carbon::now()->startOfYear());
                break;
            // 'all' - no filter
        }

        $orders = $query->get();

        // Calculate totals
        $totalEarnings = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalEarnings / $totalOrders : 0;

        // Daily breakdown for chart (last 7 days)
        $dailyEarnings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayOrders = Order::where('chef_id', $user->id)
                ->where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->get();
            
            $dailyEarnings[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'earnings' => $dayOrders->sum('total_amount'),
                'orders' => $dayOrders->count(),
            ];
        }

        // Pending payouts
        $pendingPayout = Order::where('chef_id', $user->id)
            ->where('payment_status', 'paid')
            ->where('status', 'completed')
            // Assume orders not yet paid out to vendor
            ->sum('total_amount') * 0.85; // 85% after platform fee

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'total_earnings' => $totalEarnings,
                'total_orders' => $totalOrders,
                'average_order_value' => round($avgOrderValue, 2),
                'pending_payout' => round($pendingPayout, 2),
                'daily_breakdown' => $dailyEarnings,
            ],
        ]);
    }

    /**
     * Get chef profile
     * GET /api/v1/chef/profile
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found. Please complete your profile setup.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatProfile($chefProfile, true),
        ]);
    }

    /**
     * Update chef profile
     * PUT /api/v1/chef/profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'kitchen_address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'minimum_order' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_radius_km' => 'nullable|numeric|min:0',
            'operating_hours' => 'nullable|array',
            'profile_image' => 'nullable|image|max:5120',
            'cover_image' => 'nullable|image|max:5120',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'cuisine_ids' => 'nullable|array',
            'cuisine_ids.*' => 'exists:cuisines,id',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if ($chefProfile->profile_image) {
                Storage::disk('public')->delete($chefProfile->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('chef-profiles', 'public');
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            if ($chefProfile->cover_image) {
                Storage::disk('public')->delete($chefProfile->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('chef-covers', 'public');
        }

        // Update slug if business name changed
        if (isset($validated['business_name']) && $validated['business_name'] !== $chefProfile->business_name) {
            $validated['slug'] = Str::slug($validated['business_name'] . '-' . $user->id);
        }

        $chefProfile->update($validated);

        // Sync cuisines
        if (isset($validated['cuisine_ids'])) {
            $chefProfile->cuisines()->sync($validated['cuisine_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $this->formatProfile($chefProfile->fresh(['cuisines']), true),
        ]);
    }

    /**
     * Toggle chef availability (online/offline)
     * POST /api/v1/chef/toggle-availability
     */
    public function toggleAvailability(): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found',
            ], 404);
        }

        $chefProfile->update(['is_online' => !$chefProfile->is_online]);

        return response()->json([
            'success' => true,
            'message' => $chefProfile->is_online ? 'You are now online' : 'You are now offline',
            'data' => [
                'is_online' => $chefProfile->is_online,
            ],
        ]);
    }

    /**
     * Setup/create chef profile (for new vendors or customers becoming vendors)
     * POST /api/v1/chef/profile/setup
     */
    public function setupProfile(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Check if profile already exists
        if ($user->chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile already exists. Use PUT /chef/profile to update.',
            ], 400);
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'kitchen_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'years_of_experience' => 'nullable|integer|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_radius_km' => 'nullable|numeric|min:0',
            'operating_hours' => 'nullable|array',
            'profile_image' => 'nullable|image|max:5120',
            'cover_image' => 'nullable|image|max:5120',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'cuisine_ids' => 'nullable|array',
            'cuisine_ids.*' => 'exists:cuisines,id',
        ]);

        // Handle image uploads
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('chef-profiles', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('chef-covers', 'public');
        }

        // Create slug
        $validated['slug'] = Str::slug($validated['business_name'] . '-' . $user->id);
        $validated['user_id'] = $user->id;
        $validated['verification_status'] = 'pending';

        // Create profile
        $chefProfile = ChefProfile::create($validated);

        // Assign chef role if not already assigned
        if (!$user->hasRole('chef')) {
            $user->assignRole('chef');
        }

        // Sync cuisines
        if (isset($validated['cuisine_ids'])) {
            $chefProfile->cuisines()->sync($validated['cuisine_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chef profile created successfully. Please complete verification.',
            'data' => $this->formatProfile($chefProfile->fresh(['cuisines']), true),
        ], 201);
    }

    /**
     * Upload verification documents
     * POST /api/v1/chef/documents
     */
    public function uploadDocuments(Request $request): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found. Please setup your profile first.',
            ], 404);
        }

        $validated = $request->validate([
            'document_type' => 'required|in:id_card,business_license,food_handler_certificate,other',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'description' => 'nullable|string|max:255',
        ]);

        // Store document
        $path = $request->file('document')->store('chef-documents/' . $user->id, 'public');

        // Store document info (you may want a separate ChefDocument model for this)
        // For now, we'll store in a JSON column or create simple tracking
        $documents = $chefProfile->documents ?? [];
        $documents[] = [
            'id' => Str::uuid()->toString(),
            'type' => $validated['document_type'],
            'path' => $path,
            'description' => $validated['description'] ?? null,
            'uploaded_at' => now()->toISOString(),
            'status' => 'pending',
        ];

        // Update profile (assuming documents column exists, otherwise this would need migration)
        // For simplicity, store in verification_notes as JSON
        $chefProfile->update([
            'verification_notes' => json_encode(['documents' => $documents]),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => [
                'document_type' => $validated['document_type'],
                'file_url' => asset('storage/' . $path),
                'status' => 'pending',
            ],
        ]);
    }

    /**
     * Get uploaded documents
     * GET /api/v1/chef/documents
     */
    public function getDocuments(): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found',
            ], 404);
        }

        $verificationData = json_decode($chefProfile->verification_notes, true) ?? [];
        $documents = $verificationData['documents'] ?? [];

        // Add full URLs
        $documents = array_map(function ($doc) {
            $doc['file_url'] = asset('storage/' . $doc['path']);
            unset($doc['path']);
            return $doc;
        }, $documents);

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    /**
     * Request profile verification
     * POST /api/v1/chef/request-verification
     */
    public function requestVerification(Request $request): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found',
            ], 404);
        }

        // Check if already verified
        if ($chefProfile->is_verified) {
            return response()->json([
                'success' => false,
                'message' => 'Your profile is already verified',
            ], 400);
        }

        // Check if verification is already pending
        if ($chefProfile->verification_status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Verification request already pending',
            ], 400);
        }

        // Validate required fields for verification
        $missingFields = [];
        if (empty($chefProfile->business_name)) $missingFields[] = 'business_name';
        if (empty($chefProfile->kitchen_address)) $missingFields[] = 'kitchen_address';
        if (empty($chefProfile->city)) $missingFields[] = 'city';
        if (empty($chefProfile->bank_name)) $missingFields[] = 'bank_name';
        if (empty($chefProfile->account_number)) $missingFields[] = 'account_number';

        if (!empty($missingFields)) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your profile before requesting verification',
                'data' => ['missing_fields' => $missingFields],
            ], 400);
        }

        // Update verification status
        $chefProfile->update([
            'verification_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification request submitted. You will be notified once reviewed.',
            'data' => [
                'verification_status' => 'pending',
            ],
        ]);
    }

    /**
     * Get chef's reviews
     * GET /api/v1/chef/reviews
     */
    public function reviews(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        $query = \App\Models\Review::with(['user'])
            ->where('chef_id', $user->id);

        // Filter by rating
        if ($rating = $request->input('rating')) {
            $query->where('rating', $rating);
        }

        // Sort
        $sortBy = $request->input('sort', 'latest');
        if ($sortBy === 'highest') {
            $query->orderBy('rating', 'desc');
        } elseif ($sortBy === 'lowest') {
            $query->orderBy('rating', 'asc');
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 15);
        $reviews = $query->paginate($perPage);

        // Calculate rating distribution
        $ratingDistribution = \App\Models\Review::where('chef_id', $user->id)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all ratings 1-5 are present
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = $ratingDistribution[$i] ?? 0;
        }
        ksort($ratingDistribution);

        return response()->json([
            'success' => true,
            'data' => collect($reviews->items())->map(function ($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->toISOString(),
                    'time_ago' => $review->created_at->diffForHumans(),
                    'customer' => $review->user ? [
                        'id' => $review->user->id,
                        'name' => $review->user->full_name,
                        'avatar' => $review->user->avatar_url,
                    ] : null,
                ];
            }),
            'summary' => [
                'average_rating' => (float) ($user->chefProfile?->rating ?? 0),
                'total_reviews' => $reviews->total(),
                'rating_distribution' => $ratingDistribution,
            ],
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Get detailed statistics
     * GET /api/v1/chef/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        $period = $request->input('period', '30'); // days
        $startDate = Carbon::today()->subDays((int) $period);

        // Orders over time
        $ordersOverTime = Order::where('chef_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status breakdown
        $statusBreakdown = Order::where('chef_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top selling items
        $topItems = \App\Models\OrderItem::whereHas('order', function ($q) use ($user, $startDate) {
                $q->where('chef_id', $user->id)
                    ->where('created_at', '>=', $startDate);
            })
            ->with('menu')
            ->selectRaw('menu_id, SUM(quantity) as total_quantity, SUM(price * quantity) as total_revenue')
            ->groupBy('menu_id')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'menu_id' => $item->menu_id,
                    'name' => $item->menu?->name ?? 'Unknown',
                    'total_quantity' => (int) $item->total_quantity,
                    'total_revenue' => (float) $item->total_revenue,
                ];
            });

        // Peak hours
        $peakHours = Order::where('chef_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as orders')
            ->groupBy('hour')
            ->orderByDesc('orders')
            ->take(5)
            ->get();

        // Repeat customers
        $repeatCustomers = Order::where('chef_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('user_id, COUNT(*) as order_count')
            ->groupBy('user_id')
            ->having('order_count', '>', 1)
            ->count();

        $totalCustomers = Order::where('chef_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->distinct('user_id')
            ->count('user_id');

        return response()->json([
            'success' => true,
            'data' => [
                'period_days' => (int) $period,
                'orders_over_time' => $ordersOverTime,
                'status_breakdown' => $statusBreakdown,
                'top_selling_items' => $topItems,
                'peak_hours' => $peakHours,
                'customer_stats' => [
                    'total_customers' => $totalCustomers,
                    'repeat_customers' => $repeatCustomers,
                    'repeat_rate' => $totalCustomers > 0 
                        ? round(($repeatCustomers / $totalCustomers) * 100, 1) 
                        : 0,
                ],
                'averages' => [
                    'orders_per_day' => $period > 0 
                        ? round($ordersOverTime->sum('orders') / $period, 1) 
                        : 0,
                    'revenue_per_day' => $period > 0 
                        ? round($ordersOverTime->sum('revenue') / $period, 2) 
                        : 0,
                ],
            ],
        ]);
    }

    /**
     * Update bank details
     * PUT /api/v1/chef/bank-details
     */
    public function updateBankDetails(Request $request): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:255',
        ]);

        $chefProfile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bank details updated successfully',
            'data' => [
                'bank_name' => $chefProfile->bank_name,
                'account_number' => '****' . substr($chefProfile->account_number, -4),
                'account_name' => $chefProfile->account_name,
            ],
        ]);
    }

    /**
     * Update operating hours
     * PUT /api/v1/chef/operating-hours
     */
    public function updateOperatingHours(Request $request): JsonResponse
    {
        $user = Auth::user();
        $chefProfile = $user->chefProfile;

        if (!$chefProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Chef profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'operating_hours' => 'required|array',
            'operating_hours.monday' => 'nullable|array',
            'operating_hours.tuesday' => 'nullable|array',
            'operating_hours.wednesday' => 'nullable|array',
            'operating_hours.thursday' => 'nullable|array',
            'operating_hours.friday' => 'nullable|array',
            'operating_hours.saturday' => 'nullable|array',
            'operating_hours.sunday' => 'nullable|array',
        ]);

        $chefProfile->update([
            'operating_hours' => $validated['operating_hours'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Operating hours updated successfully',
            'data' => [
                'operating_hours' => $chefProfile->operating_hours,
            ],
        ]);
    }

    // ===================== HELPER METHODS =====================

    /**
     * Format order data for API response
     */
    private function formatOrder(Order $order, bool $detailed = false): array
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'subtotal' => (float) $order->subtotal,
            'delivery_fee' => (float) $order->delivery_fee,
            'total_amount' => (float) $order->total_amount,
            'delivery_type' => $order->delivery_type ?? 'asap',
            'delivery_time_display' => $order->delivery_time_display,
            'items_count' => $order->items->count(),
            'created_at' => $order->created_at->toISOString(),
            'time_ago' => $order->created_at->diffForHumans(),
            'customer' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->full_name,
                'phone' => $order->phone_number ?? $order->user->phone,
            ] : null,
        ];

        if ($detailed) {
            $data['delivery_address'] = $order->delivery_address;
            $data['notes'] = $order->notes;
            $data['scheduled_date'] = $order->scheduled_date?->format('Y-m-d');
            $data['scheduled_time_slot'] = $order->scheduled_time_slot;
            $data['items'] = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'menu_id' => $item->menu_id,
                    'name' => $item->menu?->name ?? $item->name ?? 'Unknown Item',
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'total' => (float) ($item->price * $item->quantity),
                    'notes' => $item->notes ?? null,
                ];
            });
        }

        return $data;
    }

    /**
     * Format menu data for API response
     */
    private function formatMenu(Menu $menu): array
    {
        return [
            'id' => $menu->id,
            'name' => $menu->name,
            'slug' => $menu->slug,
            'description' => $menu->description,
            'price' => (float) $menu->price,
            'category' => $menu->category,
            'preparation_time' => $menu->preparation_time,
            'is_available' => $menu->is_available,
            'is_featured' => $menu->is_featured,
            'image_url' => $menu->image ? asset('storage/' . $menu->image) : null,
            'cuisines' => $menu->cuisines->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ]),
            'dietary_preferences' => $menu->dietaryPreferences->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
            ]),
            'created_at' => $menu->created_at->toISOString(),
        ];
    }

    /**
     * Format chef profile data for API response
     */
    private function formatProfile(ChefProfile $profile, bool $detailed = false): array
    {
        $data = [
            'id' => $profile->id,
            'user_id' => $profile->user_id,
            'business_name' => $profile->business_name,
            'slug' => $profile->slug,
            'bio' => $profile->bio,
            'city' => $profile->city,
            'is_online' => $profile->is_online,
            'is_verified' => $profile->is_verified,
            'is_featured' => $profile->is_featured,
            'rating' => (float) ($profile->rating ?? 0),
            'total_reviews' => $profile->total_reviews ?? 0,
            'total_orders' => $profile->total_orders ?? 0,
            'profile_image_url' => $profile->profile_image 
                ? asset('storage/' . $profile->profile_image) 
                : null,
            'cover_image_url' => $profile->cover_image 
                ? asset('storage/' . $profile->cover_image) 
                : null,
        ];

        if ($detailed) {
            $data['kitchen_address'] = $profile->kitchen_address;
            $data['minimum_order'] = (float) ($profile->minimum_order ?? 0);
            $data['delivery_fee'] = (float) ($profile->delivery_fee ?? 0);
            $data['delivery_radius_km'] = (float) ($profile->delivery_radius_km ?? 0);
            $data['operating_hours'] = $profile->operating_hours;
            $data['years_of_experience'] = $profile->years_of_experience;
            $data['verification_status'] = $profile->verification_status;
            $data['bank_name'] = $profile->bank_name;
            $data['account_number'] = $profile->account_number ? 
                '****' . substr($profile->account_number, -4) : null;
            $data['account_name'] = $profile->account_name;
            $data['cuisines'] = $profile->cuisines->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ]);
        }

        return $data;
    }
}
