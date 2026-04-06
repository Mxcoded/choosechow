<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'formatted_price' => '₦' . number_format($this->price, 2),
            'duration_days' => $this->duration_days,
            'duration_display' => $this->getDurationDisplay(),
            'features' => $this->features ?? [],
            'is_popular' => (bool) $this->is_popular,
            'is_active' => (bool) $this->is_active,
        ];
    }

    /**
     * Get human-readable duration display.
     */
    protected function getDurationDisplay(): string
    {
        $days = $this->duration_days;
        
        if ($days === 7) return '1 Week';
        if ($days === 14) return '2 Weeks';
        if ($days === 30) return '1 Month';
        if ($days === 90) return '3 Months';
        if ($days === 180) return '6 Months';
        if ($days === 365) return '1 Year';
        
        return $days . ' days';
    }
}
