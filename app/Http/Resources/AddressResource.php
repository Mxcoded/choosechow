<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label, // e.g., 'Home', 'Work', 'Other'
            'street_address' => $this->street_address,
            'apartment' => $this->apartment,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country ?? 'Nigeria',
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_default' => (bool) $this->is_default,
            'delivery_instructions' => $this->delivery_instructions,
            'full_address' => $this->getFullAddress(),
        ];
    }

    /**
     * Get formatted full address string.
     */
    protected function getFullAddress(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->apartment,
            $this->city,
            $this->state,
            $this->postal_code,
        ]);
        
        return implode(', ', $parts);
    }
}
