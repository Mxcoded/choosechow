<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'menu_id' => $this->menu_id,
            'quantity' => $this->quantity,
            'special_instructions' => $this->special_instructions,
            'price' => (float) $this->menu->price,
            'line_total' => (float) ($this->menu->price * $this->quantity),
            'formatted_price' => '₦' . number_format($this->menu->price, 2),
            'formatted_line_total' => '₦' . number_format($this->menu->price * $this->quantity, 2),
            
            // Menu details (always loaded)
            'menu' => [
                'id' => $this->menu->id,
                'name' => $this->menu->name,
                'slug' => $this->menu->slug,
                'description' => $this->menu->description,
                'image' => $this->menu->image ? asset('storage/' . $this->menu->image) : null,
                'is_available' => $this->menu->is_available,
                'preparation_time' => $this->menu->preparation_time,
            ],
            
            // Chef details
            'chef' => [
                'id' => $this->menu->chef->id,
                'full_name' => $this->menu->chef->full_name,
                'business_name' => $this->menu->chef->chefProfile?->business_name,
                'delivery_fee' => (float) ($this->menu->chef->chefProfile?->delivery_fee ?? 0),
                'minimum_order' => (float) ($this->menu->chef->chefProfile?->minimum_order ?? 0),
            ],
        ];
    }
}
