<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ChefProfile;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Get admin dashboard data
     * GET /api/v1/admin/dashboard
     */
    public function dashboard(): JsonResponse
    {
        // Get stats
        $stats = [
            'total_users' => User::count(),
            'total_vendors' => ChefProfile::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_approvals' => ChefProfile::where('verification_status', 'pending')->count(),
            'active_vendors' => ChefProfile::where('verification_status', 'approved')
                ->whereHas('user', fn($q) => $q->where('status', 'active'))
                ->count(),
            'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
            'today_revenue' => Order::whereDate('created_at', Carbon::today())
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];

        // Get pending vendors (vendors awaiting approval)
        $pendingVendors = ChefProfile::with('user')
            ->where('verification_status', 'pending')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($profile) => $this->formatVendor($profile));

        // Get recent orders
        $recentOrders = Order::with(['user', 'chef'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($order) => $this->formatOrder($order));

        // Get recent activity (using orders as activity log)
        $recentActivity = Order::with(['user', 'chef'])
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'type' => 'order',
                    'description' => "Order #{$order->order_number} - {$order->status}",
                    'user_id' => $order->user_id,
                    'user_name' => $order->user ? $order->user->full_name : 'Unknown',
                    'created_at' => $order->created_at->toISOString(),
                ];
            });

        return response()->json([
            'stats' => $stats,
            'pending_vendors' => $pendingVendors,
            'recent_orders' => $recentOrders,
            'recent_activity' => $recentActivity,
        ]);
    }

    /**
     * Get paginated list of users
     * GET /api/v1/admin/users
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->role($role);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $perPage = $request->input('per_page', 15);
        $users = $query->withCount('ordersPlaced')
            ->with('roles')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $users->items() ? collect($users->items())->map(fn($user) => $this->formatUser($user)) : [],
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Get single user
     * GET /api/v1/admin/users/{id}
     */
    public function showUser($id): JsonResponse
    {
        $user = User::with(['roles', 'chefProfile'])
            ->withCount('ordersPlaced')
            ->findOrFail($id);

        return response()->json([
            'data' => $this->formatUser($user),
        ]);
    }

    /**
     * Update user
     * PUT /api/v1/admin/users/{id}
     */
    public function updateUser(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|nullable|string|max:20',
            'status' => 'sometimes|in:active,suspended,banned',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $this->formatUser($user->fresh(['roles'])),
        ]);
    }

    /**
     * Delete user
     * DELETE /api/v1/admin/users/{id}
     */
    public function deleteUser($id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Don't allow deleting admin users
        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'Cannot delete admin users',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Toggle user status (active/suspended)
     * POST /api/v1/admin/users/{id}/toggle-status
     */
    public function toggleUserStatus($id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Don't allow toggling admin users
        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'Cannot modify admin users',
            ], 403);
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'message' => 'User status updated',
            'data' => $this->formatUser($user->fresh(['roles'])),
        ]);
    }

    /**
     * Get paginated list of vendors
     * GET /api/v1/admin/vendors
     */
    public function vendors(Request $request): JsonResponse
    {
        $query = ChefProfile::with('user');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            if ($status === 'pending') {
                $query->where('verification_status', 'pending');
            } elseif ($status === 'approved') {
                $query->where('verification_status', 'approved');
            } elseif ($status === 'rejected') {
                $query->where('verification_status', 'rejected');
            } elseif ($status === 'suspended') {
                $query->whereHas('user', fn($q) => $q->where('status', 'suspended'));
            }
        }

        $perPage = $request->input('per_page', 15);
        $vendors = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $vendors->items() ? collect($vendors->items())->map(fn($v) => $this->formatVendor($v)) : [],
            'meta' => [
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'per_page' => $vendors->perPage(),
                'total' => $vendors->total(),
            ],
        ]);
    }

    /**
     * Get pending vendors only
     * GET /api/v1/admin/vendors/pending
     */
    public function pendingVendors(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $vendors = ChefProfile::with('user')
            ->where('verification_status', 'pending')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $vendors->items() ? collect($vendors->items())->map(fn($v) => $this->formatVendor($v)) : [],
            'meta' => [
                'current_page' => $vendors->currentPage(),
                'last_page' => $vendors->lastPage(),
                'per_page' => $vendors->perPage(),
                'total' => $vendors->total(),
            ],
        ]);
    }

    /**
     * Approve vendor
     * POST /api/v1/admin/vendors/{id}/approve
     */
    public function approveVendor($id): JsonResponse
    {
        $vendor = ChefProfile::findOrFail($id);
        
        $vendor->update([
            'verification_status' => 'approved',
            'is_verified' => true,
        ]);

        // Also ensure user is active
        if ($vendor->user) {
            $vendor->user->update(['status' => 'active']);
        }

        return response()->json([
            'message' => 'Vendor approved successfully',
            'data' => $this->formatVendor($vendor->fresh('user')),
        ]);
    }

    /**
     * Reject vendor
     * POST /api/v1/admin/vendors/{id}/reject
     */
    public function rejectVendor(Request $request, $id): JsonResponse
    {
        $vendor = ChefProfile::findOrFail($id);
        
        $vendor->update([
            'verification_status' => 'rejected',
            'verification_notes' => $request->input('reason', 'Application rejected'),
        ]);

        return response()->json([
            'message' => 'Vendor rejected',
            'data' => $this->formatVendor($vendor->fresh('user')),
        ]);
    }

    /**
     * Suspend vendor
     * POST /api/v1/admin/vendors/{id}/suspend
     */
    public function suspendVendor(Request $request, $id): JsonResponse
    {
        $vendor = ChefProfile::findOrFail($id);
        
        // Suspend the user account
        if ($vendor->user) {
            $vendor->user->update(['status' => 'suspended']);
        }

        return response()->json([
            'message' => 'Vendor suspended',
            'data' => $this->formatVendor($vendor->fresh('user')),
        ]);
    }

    /**
     * Activate vendor
     * POST /api/v1/admin/vendors/{id}/activate
     */
    public function activateVendor($id): JsonResponse
    {
        $vendor = ChefProfile::findOrFail($id);
        
        // Activate the user account
        if ($vendor->user) {
            $vendor->user->update(['status' => 'active']);
        }

        return response()->json([
            'message' => 'Vendor activated',
            'data' => $this->formatVendor($vendor->fresh('user')),
        ]);
    }

    /**
     * Get paginated list of orders
     * GET /api/v1/admin/orders
     */
    public function orders(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'chef']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $perPage = $request->input('per_page', 15);
        $orders = $query->latest()->paginate($perPage);

        return response()->json([
            'data' => $orders->items() ? collect($orders->items())->map(fn($o) => $this->formatOrder($o)) : [],
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Get activity log
     * GET /api/v1/admin/activity
     */
    public function activity(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        
        // Using orders as activity for now
        $activities = Order::with(['user', 'chef'])
            ->latest()
            ->paginate($perPage);

        $data = collect($activities->items())->map(function ($order) {
            return [
                'id' => $order->id,
                'type' => 'order',
                'description' => "Order #{$order->order_number} - {$order->status}",
                'user_id' => $order->user_id,
                'user_name' => $order->user ? $order->user->full_name : 'Unknown',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'total' => $order->total_amount,
                ],
                'created_at' => $order->created_at->toISOString(),
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    // ===================== HELPER METHODS =====================

    /**
     * Format user data for API response
     */
    private function formatUser(User $user): array
    {
        $role = 'customer';
        if ($user->hasRole('admin')) {
            $role = 'admin';
        } elseif ($user->hasRole('chef')) {
            $role = 'chef';
        }

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
            'role' => $role,
            'roles' => $user->roles->pluck('name')->toArray(),
            'status' => $user->status ?? 'active',
            'email_verified_at' => $user->email_verified_at?->toISOString(),
            'created_at' => $user->created_at->toISOString(),
            'orders_count' => $user->orders_placed_count ?? 0,
            'total_spent' => Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];
    }

    /**
     * Format vendor data for API response
     */
    private function formatVendor(ChefProfile $profile): array
    {
        $user = $profile->user;
        
        // Map verification_status to the expected status format
        $status = $profile->verification_status;
        if ($user && $user->status === 'suspended') {
            $status = 'suspended';
        }

        return [
            'id' => $profile->id,
            'user_id' => $profile->user_id,
            'business_name' => $profile->business_name,
            'email' => $user?->email,
            'phone' => $user?->phone,
            'logo_url' => $profile->profile_image 
                ? asset('storage/' . $profile->profile_image)
                : null,
            'status' => $status,
            'is_verified' => $profile->is_verified ?? false,
            'rating' => (float) ($profile->rating ?? 0),
            'total_orders' => $profile->total_orders ?? 0,
            'total_revenue' => Order::where('chef_id', $profile->user_id)
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
            'created_at' => $profile->created_at->toISOString(),
            'user' => $user ? [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'name' => $user->full_name,
                'email' => $user->email,
                'status' => $user->status ?? 'active',
            ] : null,
        ];
    }

    /**
     * Format order data for API response
     */
    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->user?->full_name ?? 'Unknown',
            'customer_id' => $order->user_id,
            'vendor_name' => $order->chef?->full_name ?? 'Unknown',
            'vendor_id' => $order->chef_id,
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'delivery_type' => $order->delivery_type ?? 'asap',
            'created_at' => $order->created_at->toISOString(),
        ];
    }
}
