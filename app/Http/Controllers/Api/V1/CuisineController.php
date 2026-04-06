<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\CuisineResource;
use App\Models\Cuisine;
use App\Models\DietaryPreference;
use Illuminate\Http\Request;

class CuisineController extends Controller
{
    use ApiResponse;

    /**
     * List all cuisines.
     * 
     * GET /api/v1/cuisines
     */
    public function index(Request $request)
    {
        $query = Cuisine::query();

        // Only active cuisines
        if ($request->has('active_only') && $request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        // Search
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $cuisines = $query->orderBy('name')->get();

        return $this->success(CuisineResource::collection($cuisines));
    }

    /**
     * List all dietary preferences.
     * 
     * GET /api/v1/dietary-preferences
     */
    public function dietaryPreferences(Request $request)
    {
        $preferences = DietaryPreference::query()
            ->when($request->has('active_only'), function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($pref) {
                return [
                    'id' => $pref->id,
                    'name' => $pref->name,
                    'slug' => $pref->slug,
                    'description' => $pref->description,
                    'icon' => $pref->icon,
                ];
            });

        return $this->success($preferences);
    }
}
