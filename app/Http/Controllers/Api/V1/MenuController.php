<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    use ApiResponse;

    /**
     * List all available menu items with filtering.
     * 
     * GET /api/v1/menus
     * 
     * Query params:
     * - search: Search by name/description
     * - cuisine: Filter by cuisine ID
     * - dietary: Filter by dietary preference ID
     * - category: Filter by category
     * - min_price: Minimum price
     * - max_price: Maximum price
     * - chef_id: Filter by specific chef
     * - city: Filter by chef's city
     * - sort: Sort by (price_low, price_high, newest, popular)
     * - per_page: Items per page (default: 20)
     */
    public function index(Request $request)
    {
        $query = Menu::query()
            ->with(['chef.chefProfile', 'cuisines', 'dietaryPreferences'])
            ->where('is_available', true)
            ->whereHas('chef', function ($q) {
                $q->where('status', 'active')
                  ->whereHas('chefProfile', fn($p) => $p->where('is_online', true));
            });

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by cuisine
        if ($cuisineId = $request->get('cuisine')) {
            $query->whereHas('cuisines', fn($q) => $q->where('cuisines.id', $cuisineId));
        }

        // Filter by dietary preference
        if ($dietaryId = $request->get('dietary')) {
            $query->whereHas('dietaryPreferences', fn($q) => $q->where('dietary_preferences.id', $dietaryId));
        }

        // Filter by category
        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        // Filter by price range
        if ($minPrice = $request->get('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->get('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Filter by specific chef
        if ($chefId = $request->get('chef_id')) {
            $query->where('user_id', $chefId);
        }

        // Filter by city
        if ($city = $request->get('city')) {
            $query->whereHas('chef.chefProfile', fn($q) => $q->where('city', $city));
        }

        // Sorting
        $sort = $request->get('sort', 'popular');
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
            case 'popular':
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
     * Get featured menu items.
     * 
     * GET /api/v1/menus/featured
     */
    public function featured(Request $request)
    {
        $limit = min($request->get('limit', 10), 20);

        $menus = Menu::with(['chef.chefProfile', 'cuisines'])
            ->where('is_available', true)
            ->where('is_featured', true)
            ->whereHas('chef', function ($q) {
                $q->where('status', 'active')
                  ->whereHas('chefProfile', fn($p) => $p->where('is_online', true));
            })
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $this->success(MenuResource::collection($menus));
    }

    /**
     * Get a specific menu item.
     * 
     * GET /api/v1/menus/{id}
     */
    public function show($id)
    {
        $menu = Menu::with(['chef.chefProfile', 'cuisines', 'dietaryPreferences'])
            ->findOrFail($id);

        // Check if menu is available
        if (!$menu->is_available) {
            return $this->error('This menu item is currently unavailable', 400);
        }

        // Check if chef is active
        if ($menu->chef->status !== 'active') {
            return $this->notFound('Menu item not found');
        }

        return $this->success(new MenuResource($menu));
    }

    /**
     * Get all unique categories.
     * 
     * GET /api/v1/menus/categories
     */
    public function categories()
    {
        $categories = Menu::where('is_available', true)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return $this->success($categories);
    }
}
