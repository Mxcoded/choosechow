<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'menu_id' => $this->menu_id,
            'name' => $this->menu_name, // DB column is menu_name
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'total' => (float) ($this->price * $this->quantity),
            'formatted_price' => '₦' . number_format($this->price, 2),
            'formatted_total' => '₦' . number_format($this->price * $this->quantity, 2),
            'special_instructions' => $this->special_instructions,
            
            // Menu details (if still available)
            'menu' => $this->when(
                $this->relationLoaded('menu') && $this->menu,
                fn() => [
                    'id' => $this->menu->id,
                    'name' => $this->menu->name,
                    'image' => $this->menu->image ? asset('storage/' . $this->menu->image) : null,
                    'is_available' => $this->menu->is_available,
                ]
            ),
        ];
    }
}
