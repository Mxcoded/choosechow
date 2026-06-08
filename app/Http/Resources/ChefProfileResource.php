<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChefProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'business_name' => $this->business_name,
            'slug' => $this->slug,
            'bio' => $this->bio,
            'kitchen_address' => $this->kitchen_address,
            'city' => $this->city,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'profile_image' => $this->profile_image ? asset('storage/' . $this->profile_image) : null,
            'years_of_experience' => $this->years_of_experience,
            'minimum_order' => (float) $this->minimum_order,
            'delivery_fee' => (float) $this->delivery_fee,
            'delivery_radius_km' => $this->delivery_radius_km,
            'operating_hours' => $this->operating_hours,
            'is_online' => $this->is_online,
            'is_verified' => $this->is_verified,
            'is_featured' => $this->is_featured,
            'verification_status' => $this->verification_status,
            'rating' => (float) ($this->rating ?? 0),
            'total_reviews' => $this->total_reviews ?? 0,
            'total_orders' => $this->total_orders ?? 0,
            'is_open_now' => $this->isOpenNow(),
            'is_accepting_orders' => $this->isAcceptingOrders(),

            // Mobile app compatibility aliases
            'is_available' => $this->is_online,
            'description' => $this->bio,
            'banner_url' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'logo_url' => $this->profile_image ? asset('storage/' . $this->profile_image) : null,
            'address' => $this->kitchen_address ? implode(', ', array_filter([
                $this->kitchen_address,
                $this->city,
            ])) : null,
            'delivery_time' => null, // Computed delivery time TBD
            
            // Cuisines (when loaded)
            'cuisines' => $this->when(
                $this->relationLoaded('cuisines'),
                fn() => CuisineResource::collection($this->cuisines)
            ),
            
            // User info (when loaded)
            'user' => $this->when(
                $this->relationLoaded('user'),
                fn() => [
                    'id' => $this->user->id,
                    'full_name' => $this->user->full_name,
                    'avatar' => $this->user->avatar_url,
                    'average_rating' => $this->user->average_rating,
                    'review_count' => $this->user->review_count,
                ]
            ),
        ];
    }
}
