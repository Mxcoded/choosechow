<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at?->toIso8601String(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            
            // Customer info (when loaded)
            'customer' => $this->when(
                $this->relationLoaded('customer'),
                fn() => [
                    'id' => $this->customer->id,
                    'full_name' => $this->customer->full_name,
                    'avatar' => $this->customer->avatar_url,
                ]
            ),
            
            // Chef info (when loaded - for customer's review list)
            'chef' => $this->when(
                $this->relationLoaded('chef'),
                fn() => [
                    'id' => $this->chef->id,
                    'full_name' => $this->chef->full_name,
                    'business_name' => $this->chef->chefProfile?->business_name,
                ]
            ),
            
            // Order info (when loaded)
            'order' => $this->when(
                $this->relationLoaded('order'),
                fn() => [
                    'id' => $this->order->id,
                    'order_number' => $this->order->order_number,
                ]
            ),
        ];
    }
}
