<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\ChefProfileResource;
use App\Http\Resources\MenuResource;
use App\Http\Resources\ReviewResource;
use App\Models\User;
use App\Models\ChefProfile;
use App\Models\Review;
use Illuminate\Http\Request;

class ChefController extends Controller
{
    use ApiResponse;

    /**
     * List all chefs with filtering & pagination.
     * 
     * GET /api/v1/chefs
     * 
     * Query params:
     * - search: Search by name/business name
     * - cuisine: Filter by cuisine ID
     * - city: Filter by city
     * - is_online: Filter by online status
     * - is_verified: Filter by verified status
     * - min_rating: Minimum rating filter
     * - sort: Sort by (rating, orders, newest)
     * - per_page: Items per page (default: 15)
     */
    public function index(Request $request)
    {
        $query = ChefProfile::query()
            ->with(['user', 'cuisines'])
            ->whereHas('user', function ($q) {
                $q->where('status', 'active')
                  ->whereHas('roles', fn($r) => $r->where('name', 'chef'));
            });

        // Search by name or business name
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by cuisine
        if ($cuisineId = $request->get('cuisine')) {
            $query->whereHas('cuisines', fn($q) => $q->where('cuisines.id', $cuisineId));
        }

        // Filter by city
        if ($city = $request->get('city')) {
            $query->where('city', $city);
        }

        // Filter by online status
        if ($request->has('is_online')) {
            $query->where('is_online', $request->boolean('is_online'));
        }

        // Filter by verified status
        if ($request->has('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }

        // Filter by minimum rating
        if ($minRating = $request->get('min_rating')) {
            $query->where('rating', '>=', $minRating);
        }

        // Sorting
        $sort = $request->get('sort', 'rating');
        switch ($sort) {
            case 'orders':
                $query->orderByDesc('total_orders');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'rating':
            default:
                $query->orderByDesc('rating')->orderByDesc('total_reviews');
                break;
        }

        // Featured chefs first
        $query->orderByDesc('is_featured');

        $perPage = min($request->get('per_page', 15), 50);
        $chefs = $query->paginate($perPage);

        return $this->successWithPagination(
            $chefs->through(fn($chef) => new ChefProfileResource($chef))
        );
    }

    /**
     * Get a specific chef's profile.
     * 
     * GET /api/v1/chefs/{id}
     */
    public function show($id)
    {
        $chef = ChefProfile::with(['user', 'cuisines'])
            ->where('user_id', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        // Verify chef is active
        if ($chef->user->status !== 'active') {
            return $this->notFound('Chef not found');
        }

        return $this->success(new ChefProfileResource($chef));
    }

    /**
     * Get a chef's menu items.
     * 
     * GET /api/v1/chefs/{id}/menus
     */
    public function menus(Request $request, $id)
    {
        // Find chef by user_id or profile id
        $chef = ChefProfile::where('user_id', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        $query = $chef->user->menus()
            ->with(['chef.chefProfile', 'cuisines', 'dietaryPreferences'])
            ->where('is_available', true);

        // Filter by category
        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'featured':
            default:
                $query->orderByDesc('is_featured')->orderByDesc('created_at');
                break;
        }

        $perPage = min($request->get('per_page', 20), 50);
        $menus = $query->paginate($perPage);

        return $this->successWithPagination(
            $menus->through(fn($menu) => new MenuResource($menu))
        );
    }

    /**
     * Get a chef's reviews.
     * 
     * GET /api/v1/chefs/{id}/reviews
     */
    public function reviews(Request $request, $id)
    {
        // Find chef by user_id or profile id
        $chef = ChefProfile::where('user_id', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        $perPage = min($request->get('per_page', 10), 50);
        
        $reviews = Review::where('chef_id', $chef->user_id)
            ->with(['customer'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        // Calculate rating summary
        $ratingStats = Review::where('chef_id', $chef->user_id)
            ->selectRaw('
                COUNT(*) as total,
                AVG(rating) as average,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            ')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'data' => ReviewResource::collection($reviews->items()),
            'summary' => [
                'total_reviews' => $ratingStats->total,
                'average_rating' => round($ratingStats->average ?? 0, 1),
                'rating_breakdown' => [
                    5 => (int) $ratingStats->five_star,
                    4 => (int) $ratingStats->four_star,
                    3 => (int) $ratingStats->three_star,
                    2 => (int) $ratingStats->two_star,
                    1 => (int) $ratingStats->one_star,
                ],
            ],
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }
}
