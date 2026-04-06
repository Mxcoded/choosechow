<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->user_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'formatted_price' => '₦' . number_format($this->price, 2),
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'images' => $this->getImagesArray(),
            'category' => $this->category,
            'preparation_time' => $this->preparation_time,
            'preparation_time_display' => $this->preparation_time ? $this->preparation_time . ' mins' : null,
            'is_available' => (bool) $this->is_available,
            'is_featured' => (bool) $this->is_featured,
            'created_at' => $this->created_at?->toIso8601String(),
            
            // Chef info (when loaded)
            'chef' => $this->when(
                $this->relationLoaded('chef'),
                fn() => [
                    'id' => $this->chef->id,
                    'full_name' => $this->chef->full_name,
                    'avatar' => $this->chef->avatar_url,
                    'business_name' => $this->chef->chefProfile?->business_name,
                    'is_verified' => $this->chef->chefProfile?->is_verified ?? false,
                    'rating' => $this->chef->average_rating,
                    'delivery_fee' => (float) ($this->chef->chefProfile?->delivery_fee ?? 0),
                ]
            ),
            
            // Cuisines (when loaded)
            'cuisines' => $this->when(
                $this->relationLoaded('cuisines'),
                fn() => CuisineResource::collection($this->cuisines)
            ),
            
            // Dietary preferences (when loaded)
            'dietary_preferences' => $this->when(
                $this->relationLoaded('dietaryPreferences'),
                fn() => $this->dietaryPreferences->pluck('name')
            ),
        ];
    }

    /**
     * Get images as array with full URLs.
     */
    protected function getImagesArray(): array
    {
        if ($this->image) {
            return [asset('storage/' . $this->image)];
        }
        return [];
    }
}
