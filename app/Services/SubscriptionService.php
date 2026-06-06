<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\ChefProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function __construct(
        protected DiscountService $discountService,
        protected BillingService $billingService,
        protected PaystackService $paystack,
    ) {}

    public function subscribe(User $user, string $tier, ?string $paymentMethod = null): array
    {
        $plan = SubscriptionPlan::where('slug', $tier)
            ->where('user_type', 'customer')
            ->where('is_active', true)
            ->firstOrFail();

        if ($user->subscription_status === 'active' && $user->subscription_tier !== 'none') {
            return ['success' => false, 'message' => 'You already have an active subscription.'];
        }

        if ($tier === $this->discountService::TIER_NONE) {
            return $this->cancelToNone($user);
        }

        if ($paymentMethod === 'wallet') {
            return $this->subscribeWithWallet($user, $plan);
        }

        if ($paymentMethod === 'paystack') {
            return $this->initiatePaystackSubscription($user, $plan);
        }

        return $this->activateSubscription($user, $plan);
    }

    public function upgrade(User $user, string $newTier, ?string $paymentMethod = null): array
    {
        $allowedUpgrades = ['basic' => 'plus', 'plus' => 'premium'];

        if (!isset($allowedUpgrades[$user->subscription_tier]) || !in_array($newTier, [$allowedUpgrades[$user->subscription_tier], 'premium'])) {
            return ['success' => false, 'message' => 'Invalid upgrade path.'];
        }

        if ($user->subscription_tier === $newTier) {
            return ['success' => false, 'message' => "Already on the {$newTier} tier."];
        }

        $currentPlan = SubscriptionPlan::where('slug', $user->subscription_tier)->first();
        $newPlan = SubscriptionPlan::where('slug', $newTier)->firstOrFail();
        $proratedAmount = $this->billingService->calculateProratedAmount($currentPlan, $newPlan);

        if ($proratedAmount > 0 && $paymentMethod === 'wallet') {
            return $this->upgradeWithWallet($user, $newPlan, $currentPlan, $proratedAmount);
        }

        if ($proratedAmount > 0 && $paymentMethod === 'paystack') {
            return $this->initiatePaystackUpgrade($user, $newPlan, $currentPlan, $proratedAmount);
        }

        return $this->applyUpgrade($user, $newPlan, $currentPlan, $proratedAmount);
    }

    public function downgrade(User $user, string $newTier): array
    {
        $allowedDowngrades = ['premium' => ['plus', 'basic'], 'plus' => ['basic']];

        if (!isset($allowedDowngrades[$user->subscription_tier]) || !in_array($newTier, $allowedDowngrades[$user->subscription_tier])) {
            return ['success' => false, 'message' => 'Invalid downgrade path.'];
        }

        if ($user->subscription_tier === $newTier) {
            return ['success' => false, 'message' => "Already on the {$newTier} tier."];
        }

        $newPlan = SubscriptionPlan::where('slug', $newTier)->firstOrFail();

        return DB::transaction(function () use ($user, $newTier, $newPlan) {
            $subscription = $user->activeSubscription;
            if ($subscription) {
                $subscription->update([
                    'subscription_plan_id' => $newPlan->id,
                    'amount' => $newPlan->monthly_price,
                    'cancelled_at' => null,
                    'ends_at' => null,
                ]);
            }

            $user->update([
                'subscription_tier' => $newTier,
                'subscription_status' => 'active',
            ]);

            $this->clearDedicatedRider($user);

            return [
                'success' => true,
                'message' => "Downgrade to {$newPlan->name} will take effect next billing cycle.",
                'effective_at' => $subscription?->current_period_end ?? now()->addMonth(),
            ];
        });
    }

    public function cancel(User $user, bool $immediately = false): array
    {
        return DB::transaction(function () use ($user, $immediately) {
            $subscription = $user->activeSubscription;

            if (!$subscription) {
                return ['success' => false, 'message' => 'No active subscription to cancel.'];
            }

            if ($immediately) {
                $subscription->cancel(true);
                $user->update([
                    'subscription_tier' => 'none',
                    'subscription_status' => 'cancelled',
                    'renews_at' => null,
                ]);
                $this->clearDedicatedRider($user);
                return ['success' => true, 'message' => 'Subscription cancelled immediately.'];
            }

            $subscription->cancel();
            $user->update([
                'subscription_status' => 'cancelled',
            ]);

            return [
                'success' => true,
                'message' => 'Subscription cancelled. Benefits continue until ' . $subscription->ends_at->format('M j, Y') . '.',
                'ends_at' => $subscription->ends_at,
            ];
        });
    }

    public function getStatus(User $user): array
    {
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return [
                'tier' => 'none',
                'status' => 'none',
                'is_active' => false,
                'renews_at' => null,
            ];
        }

        $plan = $subscription->plan;

        return [
            'tier' => $user->subscription_tier,
            'status' => $user->subscription_status,
            'is_active' => $subscription->isActive(),
            'plan_name' => $plan?->name,
            'plan_price' => (float)$subscription->amount,
            'started_at' => $subscription->current_period_start,
            'renews_at' => $subscription->current_period_end,
            'cancelled_at' => $subscription->cancelled_at,
            'ends_at' => $subscription->ends_at,
            'free_delivery_used' => $user->free_delivery_used,
            'free_delivery_limit' => $this->discountService->getTierLimit('basic', 'free_deliveries', 1),
            'has_dedicated_rider' => $user->dedicated_rider_id !== null,
            'benefits' => $plan?->features ?? [],
        ];
    }

    public function calculateCheckout(User $user, float $subtotal, float $standardDeliveryFee, array $items): array
    {
        $tier = $user->subscription_tier ?? 'none';
        $totalDiscount = 0;

        $discountedItems = array_map(function ($item) use ($user, &$totalDiscount) {
            $isPartner = isset($item['chef_id'])
                ? ChefProfile::where('user_id', $item['chef_id'])->value('is_partner') ?? false
                : ($item['is_partner'] ?? false);

            $discount = $this->discountService->calculateItemDiscount(
                $item['price'] * $item['quantity'],
                $user,
                $isPartner
            );
            $totalDiscount += $discount;

            return array_merge($item, [
                'original_price' => $item['price'],
                'discount' => $discount,
                'final_price' => $item['price'] - ($discount / max($item['quantity'], 1)),
            ]);
        }, $items);

        $deliveryFee = $this->discountService->calculateDeliveryFee($standardDeliveryFee, $user);
        $orderPriority = $this->discountService->getOrderPriority($user);
        $finalSubtotal = $subtotal - $totalDiscount;

        return [
            'original_subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'final_subtotal' => $finalSubtotal,
            'standard_delivery_fee' => $standardDeliveryFee,
            'delivery_fee' => $deliveryFee,
            'delivery_fee_saved' => $standardDeliveryFee - $deliveryFee,
            'order_priority' => $orderPriority,
            'items' => $discountedItems,
            'applied_tier' => $tier,
        ];
    }

    public function useFreeDelivery(User $user): void
    {
        if ($user->subscription_tier === 'basic') {
            $user->increment('free_delivery_used');
        }
    }

    /**
     * Verify Paystack payment and activate a pending subscription.
     */
    public function verifyAndActivate(User $user, string $reference): array
    {
        $payment = Payment::where('reference', $reference)
            ->where('user_id', $user->id)
            ->where('type', 'subscription')
            ->first();

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found.'];
        }

        if ($payment->isSuccessful()) {
            return ['success' => false, 'message' => 'Payment already processed.'];
        }

        $result = $this->paystack->verifyTransaction($reference);

        if (!isset($result['status']) || $result['status'] !== true) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $result['message'] ?? 'Paystack verification failed',
            ]);
            return ['success' => false, 'message' => $result['message'] ?? 'Payment verification failed.'];
        }

        return DB::transaction(function () use ($user, $payment, $result) {
            $payment->markAsSuccessful($result);

            $plan = SubscriptionPlan::find($payment->metadata['plan_id'] ?? 0);
            if (!$plan) {
                return ['success' => false, 'message' => 'Subscription plan not found.'];
            }

            $tier = $plan->slug;

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'amount' => $plan->monthly_price,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            $user->update([
                'subscription_tier' => $tier,
                'subscription_status' => 'active',
                'renews_at' => now()->addMonth(),
                'free_delivery_used' => 0,
            ]);

            if ($tier === 'premium') {
                $this->assignDedicatedRider($user);
                $this->billingService->creditPremiumWallet($user);
            }

            return [
                'success' => true,
                'subscription' => $subscription,
                'message' => "Subscribed to {$plan->name} successfully.",
            ];
        });
    }

    public function verifyAndApplyUpgrade(User $user, string $reference): array
    {
        $payment = Payment::where('reference', $reference)
            ->where('user_id', $user->id)
            ->where('type', 'subscription_upgrade')
            ->first();

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found.'];
        }

        if ($payment->isSuccessful()) {
            return ['success' => false, 'message' => 'Payment already processed.'];
        }

        $result = $this->paystack->verifyTransaction($reference);

        if (!isset($result['status']) || $result['status'] !== true) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $result['message'] ?? 'Paystack verification failed',
            ]);
            return ['success' => false, 'message' => $result['message'] ?? 'Payment verification failed.'];
        }

        return DB::transaction(function () use ($user, $payment, $result) {
            $payment->markAsSuccessful($result);

            $newPlan = SubscriptionPlan::find($payment->metadata['plan_id'] ?? 0);
            $currentPlan = SubscriptionPlan::find($payment->metadata['current_plan_id'] ?? 0);

            if (!$newPlan || !$currentPlan) {
                return ['success' => false, 'message' => 'Subscription plans not found.'];
            }

            $proratedAmount = $this->billingService->calculateProratedAmount($currentPlan, $newPlan);

            return $this->applyUpgrade($user, $newPlan, $currentPlan, $proratedAmount);
        });
    }

    // ===================== INTERNAL HELPERS =====================

    protected function subscribeWithWallet(User $user, SubscriptionPlan $plan): array
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        if (!$wallet->hasSufficientBalance($plan->monthly_price)) {
            return [
                'success' => false,
                'message' => 'Insufficient wallet balance. You need ₦' . number_format($plan->monthly_price) . '.',
            ];
        }

        return DB::transaction(function () use ($user, $plan, $wallet) {
            $payment = Payment::create([
                'reference' => Payment::generateReference(),
                'user_id' => $user->id,
                'amount' => $plan->monthly_price,
                'currency' => 'NGN',
                'type' => 'subscription',
                'status' => 'success',
                'payment_method' => 'wallet',
                'gateway' => 'wallet',
                'paid_at' => now(),
            ]);

            $wallet->deduct(
                $plan->monthly_price,
                $payment->reference,
                "Subscription payment: {$plan->name}"
            );

            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'amount' => $plan->monthly_price,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            $user->update([
                'subscription_tier' => $plan->slug,
                'subscription_status' => 'active',
                'renews_at' => now()->addMonth(),
                'free_delivery_used' => 0,
            ]);

            if ($plan->slug === 'premium') {
                $this->assignDedicatedRider($user);
                $this->billingService->creditPremiumWallet($user);
            }

            return [
                'success' => true,
                'subscription' => $subscription,
                'message' => "Subscribed to {$plan->name} via wallet.",
            ];
        });
    }

    protected function initiatePaystackSubscription(User $user, SubscriptionPlan $plan): array
    {
        $payment = Payment::create([
            'reference' => Payment::generateReference(),
            'user_id' => $user->id,
            'amount' => $plan->monthly_price,
            'currency' => 'NGN',
            'type' => 'subscription',
            'status' => 'pending',
            'payment_method' => 'paystack',
            'gateway' => 'paystack',
            'metadata' => ['plan_id' => $plan->id, 'plan_slug' => $plan->slug],
        ]);

        $result = $this->paystack->initializeTransaction(
            $user->email,
            $plan->monthly_price,
            $payment->reference,
        );

        if (!isset($result['status']) || !$result['status']) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $result,
                'failure_reason' => $result['message'] ?? 'Paystack initialization failed',
            ]);
            return ['success' => false, 'message' => $result['message'] ?? 'Payment initiation failed'];
        }

        $payment->update([
            'gateway_reference' => $result['data']['reference'] ?? null,
            'gateway_response' => $result,
            'metadata' => array_merge($payment->metadata ?? [], [
                'authorization_url' => $result['data']['authorization_url'] ?? null,
                'access_code' => $result['data']['access_code'] ?? null,
            ]),
        ]);

        return [
            'success' => true,
            'authorization_url' => $result['data']['authorization_url'] ?? null,
            'reference' => $payment->reference,
            'message' => 'Proceed to payment.',
        ];
    }

    protected function activateSubscription(User $user, SubscriptionPlan $plan): array
    {
        return DB::transaction(function () use ($user, $plan) {
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'amount' => $plan->monthly_price,
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);

            $user->update([
                'subscription_tier' => $plan->slug,
                'subscription_status' => 'active',
                'renews_at' => now()->addMonth(),
                'free_delivery_used' => 0,
            ]);

            if ($plan->slug === 'premium') {
                $this->assignDedicatedRider($user);
                $this->billingService->creditPremiumWallet($user);
            }

            return [
                'success' => true,
                'subscription' => $subscription,
                'message' => "Subscribed to {$plan->name} successfully.",
            ];
        });
    }

    protected function upgradeWithWallet(User $user, SubscriptionPlan $newPlan, SubscriptionPlan $currentPlan, float $proratedAmount): array
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        if (!$wallet->hasSufficientBalance($proratedAmount)) {
            return [
                'success' => false,
                'message' => 'Insufficient wallet balance for prorated charge of ₦' . number_format($proratedAmount) . '.',
            ];
        }

        return DB::transaction(function () use ($user, $newPlan, $currentPlan, $proratedAmount, $wallet) {
            $payment = Payment::create([
                'reference' => Payment::generateReference(),
                'user_id' => $user->id,
                'amount' => $proratedAmount,
                'currency' => 'NGN',
                'type' => 'subscription_upgrade',
                'status' => 'success',
                'payment_method' => 'wallet',
                'gateway' => 'wallet',
                'paid_at' => now(),
            ]);

            $wallet->deduct(
                $proratedAmount,
                $payment->reference,
                "Upgrade prorated charge: {$currentPlan->name} → {$newPlan->name}"
            );

            return $this->applyUpgrade($user, $newPlan, $currentPlan, $proratedAmount);
        });
    }

    protected function initiatePaystackUpgrade(User $user, SubscriptionPlan $newPlan, SubscriptionPlan $currentPlan, float $proratedAmount): array
    {
        $payment = Payment::create([
            'reference' => Payment::generateReference(),
            'user_id' => $user->id,
            'amount' => $proratedAmount,
            'currency' => 'NGN',
            'type' => 'subscription_upgrade',
            'status' => 'pending',
            'payment_method' => 'paystack',
            'gateway' => 'paystack',
            'metadata' => [
                'plan_id' => $newPlan->id,
                'plan_slug' => $newPlan->slug,
                'current_plan_id' => $currentPlan->id,
                'current_plan_slug' => $currentPlan->slug,
            ],
        ]);

        $result = $this->paystack->initializeTransaction(
            $user->email,
            $proratedAmount,
            $payment->reference,
        );

        if (!isset($result['status']) || !$result['status']) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $result,
                'failure_reason' => $result['message'] ?? 'Paystack initialization failed',
            ]);
            return ['success' => false, 'message' => $result['message'] ?? 'Payment initiation failed'];
        }

        $payment->update([
            'gateway_reference' => $result['data']['reference'] ?? null,
            'gateway_response' => $result,
            'metadata' => array_merge($payment->metadata ?? [], [
                'authorization_url' => $result['data']['authorization_url'] ?? null,
                'access_code' => $result['data']['access_code'] ?? null,
            ]),
        ]);

        return [
            'success' => true,
            'authorization_url' => $result['data']['authorization_url'] ?? null,
            'reference' => $payment->reference,
            'prorated_charge' => $proratedAmount,
            'message' => "Proceed to pay prorated charge of ₦{$proratedAmount}.",
        ];
    }

    protected function applyUpgrade(User $user, SubscriptionPlan $newPlan, SubscriptionPlan $currentPlan, float $proratedAmount): array
    {
        return DB::transaction(function () use ($user, $newPlan, $currentPlan, $proratedAmount) {
            $newTier = $newPlan->slug;

            $subscription = $user->activeSubscription;
            if ($subscription) {
                $subscription->update([
                    'subscription_plan_id' => $newPlan->id,
                    'amount' => $newPlan->monthly_price,
                    'status' => 'active',
                ]);
            }

            $user->update([
                'subscription_tier' => $newTier,
                'subscription_status' => 'active',
                'free_delivery_used' => 0,
            ]);

            if ($newTier === 'premium') {
                $this->assignDedicatedRider($user);
            }

            $result = [
                'success' => true,
                'message' => "Upgraded to {$newPlan->name}. ",
                'subscription' => $subscription,
            ];

            if ($proratedAmount > 0) {
                $result['prorated_charge'] = $proratedAmount;
                $result['message'] .= "Prorated charge of ₦{$proratedAmount} applied.";
            } else {
                $result['message'] .= "No additional charge for this billing period.";
            }

            return $result;
        });
    }

    protected function assignDedicatedRider(User $user): void
    {
        $rider = User::role('rider')->inRandomOrder()->first();

        if ($rider) {
            $user->update(['dedicated_rider_id' => $rider->id]);
        } else {
            $user->update(['dedicated_rider_id' => null]);
        }
    }

    protected function clearDedicatedRider(User $user): void
    {
        $user->update(['dedicated_rider_id' => null]);
    }

    protected function cancelToNone(User $user): array
    {
        return $this->cancel($user, true);
    }
}
