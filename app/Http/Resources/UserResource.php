<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar_url,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'referral_code' => $this->referral_code,
            'email_verified' => $this->hasVerifiedEmail(),
            'phone_verified' => $this->phone_verified_at !== null,
            'status' => $this->status,
            'roles' => $this->getRoleNames(),
            'is_chef' => $this->isChef(),
            'is_customer' => $this->isCustomer(),
            'is_admin' => $this->isAdmin(),
            'preferences' => $this->preferences,
            'created_at' => $this->created_at?->toIso8601String(),
            
            // Relationships (when loaded)
            'chef_profile' => $this->when(
                $this->relationLoaded('chefProfile') && $this->chefProfile,
                fn() => new ChefProfileResource($this->chefProfile)
            ),
            'addresses' => $this->when(
                $this->relationLoaded('addresses'),
                fn() => AddressResource::collection($this->addresses)
            ),
            'active_subscription' => $this->when(
                $this->relationLoaded('activeSubscription') && $this->activeSubscription,
                fn() => new UserSubscriptionResource($this->activeSubscription)
            ),
            'default_address' => $this->when(
                $this->relationLoaded('addresses'),
                fn() => $this->addresses->where('is_default', true)->first() 
                    ? new AddressResource($this->addresses->where('is_default', true)->first())
                    : null
            ),
            
            // Stats (for profile display)
            'stats' => $this->when($this->isChef(), fn() => [
                'average_rating' => $this->average_rating,
                'review_count' => $this->review_count,
                'total_orders' => $this->chefProfile?->total_orders ?? 0,
            ]),
        ];
    }
}
