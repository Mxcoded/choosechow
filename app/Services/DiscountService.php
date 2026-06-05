<?php

namespace App\Services;

use App\Models\User;
use App\Models\SubscriptionPlan;

class DiscountService
{
    const TIER_BASIC = 'basic';
    const TIER_PLUS = 'plus';
    const TIER_PREMIUM = 'premium';
    const TIER_NONE = 'none';

    public function calculateDeliveryFee(float $standardFee, User $user, bool $isPartnerChef = false): float
    {
        $tier = $user->subscription_tier ?? self::TIER_NONE;

        return match ($tier) {
            self::TIER_PREMIUM => 0,
            self::TIER_PLUS => $this->calculatePlusDelivery($standardFee, $user),
            self::TIER_BASIC => $this->calculateBasicDelivery($standardFee, $user),
            default => $standardFee,
        };
    }

    public function calculateItemDiscount(float $itemPrice, User $user, bool $isPartnerChef = false): float
    {
        $tier = $user->subscription_tier ?? self::TIER_NONE;

        return match ($tier) {
            self::TIER_PREMIUM => round($itemPrice * 0.20, 2),
            self::TIER_PLUS => $isPartnerChef ? round($itemPrice * 0.15, 2) : 0,
            default => 0,
        };
    }

    public function getOrderPriority(User $user): int
    {
        $tier = $user->subscription_tier ?? self::TIER_NONE;

        return match ($tier) {
            self::TIER_PREMIUM => 3,
            self::TIER_PLUS => 2,
            default => 1,
        };
    }

    public function getSlaMinutes(User $user): int
    {
        $tier = $user->subscription_tier ?? self::TIER_NONE;

        return match ($tier) {
            self::TIER_PREMIUM => 10,
            self::TIER_PLUS => 60,
            default => 1440,
        };
    }

    public function getSlaLevel(User $user): string
    {
        $tier = $user->subscription_tier ?? self::TIER_NONE;

        return match ($tier) {
            self::TIER_PREMIUM => 'vip',
            self::TIER_PLUS => 'priority',
            default => 'standard',
        };
    }

    public function canScheduleOrders(User $user): bool
    {
        return ($user->subscription_tier ?? self::TIER_NONE) === self::TIER_PREMIUM;
    }

    protected function calculatePlusDelivery(float $fee, User $user): float
    {
        $minSubtotal = $this->getTierLimit(self::TIER_PLUS, 'free_delivery_min_subtotal', 8000);
        return $fee;
    }

    public function isPlusDeliveryFree(?float $subtotal): bool
    {
        if ($subtotal === null) {
            return false;
        }
        return $subtotal >= $this->getTierLimit(self::TIER_PLUS, 'free_delivery_min_subtotal', 8000);
    }

    protected function calculateBasicDelivery(float $fee, User $user): float
    {
        $plan = SubscriptionPlan::where('slug', self::TIER_BASIC)->first();
        $limit = $plan?->limits['free_deliveries'] ?? 1;
        $discountPercent = $plan?->limits['delivery_discount_percent'] ?? 10;

        if ($user->free_delivery_used < $limit) {
            return 0;
        }

        return round($fee * (1 - $discountPercent / 100), 2);
    }

    public function getTierLimit(string $tier, string $key, mixed $default = null): mixed
    {
        $plan = SubscriptionPlan::where('slug', $tier)->first();
        return $plan?->limits[$key] ?? $default;
    }

    public function getTierMonthlyPrice(string $tier): float
    {
        $plan = SubscriptionPlan::where('slug', $tier)->first();
        return $plan?->monthly_price ?? 0;
    }
}
