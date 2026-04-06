<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'status_display' => ucfirst($this->status),
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'is_active' => $this->isActive(),
            'is_on_trial' => $this->status === 'trial',
            'days_remaining' => $this->getDaysRemaining(),
            
            // Plan details (when loaded)
            'plan' => $this->when(
                $this->relationLoaded('plan'),
                fn() => new SubscriptionPlanResource($this->plan)
            ),
        ];
    }

    /**
     * Check if subscription is active.
     */
    protected function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']) 
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Get days remaining in subscription.
     */
    protected function getDaysRemaining(): ?int
    {
        if (!$this->ends_at) {
            return null;
        }
        
        return max(0, now()->diffInDays($this->ends_at, false));
    }
}
