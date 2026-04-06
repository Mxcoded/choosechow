<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'status_display' => $this->getStatusDisplay(),
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'subtotal' => (float) $this->subtotal,
            'delivery_fee' => (float) $this->delivery_fee,
            'total_amount' => (float) $this->total_amount,
            'formatted_total' => '₦' . number_format($this->total_amount, 2),
            'delivery_address' => $this->delivery_address,
            'phone_number' => $this->phone_number,
            'notes' => $this->notes,
            'delivery_type' => $this->delivery_type ?? 'asap',
            'scheduled_date' => $this->scheduled_date?->format('Y-m-d'),
            'scheduled_time_slot' => $this->scheduled_time_slot,
            'delivery_time_display' => $this->delivery_time_display,
            'is_scheduled' => $this->isScheduled(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            
            // Order items (when loaded)
            'items' => $this->when(
                $this->relationLoaded('items'),
                fn() => OrderItemResource::collection($this->items)
            ),
            'items_count' => $this->when(
                $this->relationLoaded('items'),
                fn() => $this->items->sum('quantity')
            ),
            
            // Chef info (when loaded)
            'chef' => $this->when(
                $this->relationLoaded('chef'),
                fn() => [
                    'id' => $this->chef->id,
                    'full_name' => $this->chef->full_name,
                    'avatar' => $this->chef->avatar_url,
                    'phone' => $this->chef->phone,
                    'business_name' => $this->chef->chefProfile?->business_name,
                ]
            ),
            
            // Customer info (when loaded - for chef view)
            'customer' => $this->when(
                $this->relationLoaded('user'),
                fn() => [
                    'id' => $this->user->id,
                    'full_name' => $this->user->full_name,
                    'avatar' => $this->user->avatar_url,
                    'phone' => $this->user->phone,
                ]
            ),
            
            // Can perform actions
            'can_cancel' => $this->canBeCancelled(),
            'can_review' => $this->canBeReviewed(),
        ];
    }

    /**
     * Get human-readable status display.
     */
    protected function getStatusDisplay(): string
    {
        return match($this->status) {
            'pending_payment' => 'Awaiting Payment',
            'pending' => 'Order Placed',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'ready' => 'Ready for Pickup/Delivery',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Check if order can be cancelled.
     */
    protected function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending_payment', 'pending', 'confirmed']);
    }

    /**
     * Check if order can be reviewed.
     */
    protected function canBeReviewed(): bool
    {
        return in_array($this->status, ['delivered', 'completed']);
    }
}
