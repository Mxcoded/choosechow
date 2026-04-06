<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\ChefProfileResource;
use App\Models\Favorite;
use App\Models\ChefProfile;
use App\Models\User;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponse;

    /**
     * Get user's favorite chefs.
     * 
     * GET /api/v1/favorites
     */
    public function index(Request $request)
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->where('favoritable_type', User::class)
            ->with(['favoritable.chefProfile.cuisines'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($favorite) {
                $chef = $favorite->favoritable;
                if ($chef && $chef->chefProfile) {
                    return new ChefProfileResource($chef->chefProfile);
                }
                return null;
            })
            ->filter();

        return $this->success($favorites->values());
    }

    /**
     * Add chef to favorites.
     * 
     * POST /api/v1/favorites/{chefId}
     */
    public function store(Request $request, $chefId)
    {
        // Verify chef exists
        $chefProfile = ChefProfile::where('user_id', $chefId)
            ->orWhere('id', $chefId)
            ->first();

        if (!$chefProfile) {
            return $this->notFound('Chef not found');
        }

        $chef = $chefProfile->user;

        // Check if already favorited using the model's static method
        if (Favorite::isFavorited($request->user()->id, $chef)) {
            return $this->error('Chef is already in your favorites', 400);
        }

        Favorite::create([
            'user_id' => $request->user()->id,
            'favoritable_type' => User::class,
            'favoritable_id' => $chef->id,
        ]);

        return $this->created(null, 'Chef added to favorites');
    }

    /**
     * Remove chef from favorites.
     * 
     * DELETE /api/v1/favorites/{chefId}
     */
    public function destroy(Request $request, $chefId)
    {
        // Find chef profile to get user_id
        $chefProfile = ChefProfile::where('user_id', $chefId)
            ->orWhere('id', $chefId)
            ->first();

        if (!$chefProfile) {
            return $this->notFound('Chef not found');
        }

        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('favoritable_type', User::class)
            ->where('favoritable_id', $chefProfile->user_id)
            ->first();

        if (!$favorite) {
            return $this->notFound('Chef not in favorites');
        }

        $favorite->delete();

        return $this->success(null, 'Chef removed from favorites');
    }

    /**
     * Check if a chef is favorited.
     * 
     * GET /api/v1/favorites/check/{chefId}
     */
    public function check(Request $request, $chefId)
    {
        // Find chef profile to get user_id
        $chefProfile = ChefProfile::where('user_id', $chefId)
            ->orWhere('id', $chefId)
            ->first();

        if (!$chefProfile) {
            return $this->success(['is_favorited' => false]);
        }

        $isFavorited = Favorite::where('user_id', $request->user()->id)
            ->where('favoritable_type', User::class)
            ->where('favoritable_id', $chefProfile->user_id)
            ->exists();

        return $this->success([
            'is_favorited' => $isFavorited,
        ]);
    }
}
