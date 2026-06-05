<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletTransactionLog;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    const MAX_RETRIES = 3;
    const GRACE_PERIOD_DAYS = 3;
    const RETRY_INTERVAL_HOURS = 24;

    public function __construct(
        protected PaystackService $paystack,
        protected DiscountService $discountService,
    ) {}

    public function charge(UserSubscription $subscription): array
    {
        $user = $subscription->user;
        $amount = $subscription->amount;

        $payment = Payment::create([
            'reference' => Payment::generateReference(),
            'user_id' => $user->id,
            'payable_type' => get_class($subscription),
            'payable_id' => $subscription->id,
            'amount' => $amount,
            'currency' => 'NGN',
            'type' => 'subscription',
            'status' => 'pending',
            'payment_method' => 'paystack',
            'gateway' => 'paystack',
        ]);

        $result = $this->paystack->initializeTransaction(
            $user->email,
            $amount,
            $payment->reference
        );

        if (!isset($result['status']) || !$result['status']) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $result,
                'failure_reason' => $result['message'] ?? 'Paystack initialization failed',
            ]);

            $this->incrementRetry($subscription);
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
            'payment' => $payment,
        ];
    }

    public function processRenewal(UserSubscription $subscription): array
    {
        $user = $subscription->user;

        if (!$user->wallet) {
            Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        }

        $result = $this->charge($subscription);

        if (!$result['success']) {
            return $result;
        }

        return $result;
    }

    public function completeRenewal(UserSubscription $subscription, Payment $payment): void
    {
        DB::transaction(function () use ($subscription, $payment) {
            $subscription->update([
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
                'status' => 'active',
            ]);

            $payment->markAsSuccessful();

            $this->resetPeriodBenefits($subscription->user);

            $this->creditPremiumWallet($subscription->user);
        });
    }

    public function resetPeriodBenefits(User $user): void
    {
        $user->update([
            'free_delivery_used' => 0,
            'renews_at' => now()->addMonth(),
        ]);
    }

    public function creditPremiumWallet(User $user): void
    {
        if ($user->subscription_tier !== 'premium') {
            return;
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $credit = $this->discountService->getTierLimit('premium', 'monthly_wallet_credit', 500);

        $wallet->logTransaction(
            'subscription_credit',
            $credit,
            'SUB_CREDIT_' . now()->format('Ym'),
            'Monthly Premium subscription wallet credit',
        );
    }

    public function incrementRetry(UserSubscription $subscription): void
    {
        $retries = (int)($subscription->metadata['payment_retries'] ?? 0) + 1;
        $metadata = $subscription->metadata ?? [];
        $metadata['payment_retries'] = $retries;
        $metadata['last_retry_at'] = now()->toDateTimeString();

        $subscription->update(['metadata' => $metadata]);

        if ($retries >= self::MAX_RETRIES) {
            $subscription->update(['status' => 'past_due']);
            $user = $subscription->user;
            $user->update(['subscription_status' => 'past_due']);
        }
    }

    public function shouldRetry(UserSubscription $subscription): bool
    {
        if (!in_array($subscription->status, ['active', 'past_due'])) {
            return false;
        }

        $retries = (int)($subscription->metadata['payment_retries'] ?? 0);

        if ($retries >= self::MAX_RETRIES) {
            return false;
        }

        $lastRetry = $subscription->metadata['last_retry_at'] ?? null;
        if (!$lastRetry) {
            return true;
        }

        $lastRetryTime = strtotime($lastRetry);
        return (now()->timestamp - $lastRetryTime) >= (self::RETRY_INTERVAL_HOURS * 3600);
    }

    public function applyGracePeriod(User $user): void
    {
        if ($user->subscription_status !== 'past_due') {
            return;
        }

        $graceEnd = $user->renews_at?->addDays(self::GRACE_PERIOD_DAYS);

        if ($graceEnd && now()->greaterThan($graceEnd)) {
            $user->update([
                'subscription_status' => 'expired',
                'subscription_tier' => 'none',
            ]);

            UserSubscription::where('user_id', $user->id)
                ->whereIn('status', ['active', 'past_due'])
                ->update(['status' => 'expired']);
        }
    }

    public function calculateProratedAmount(SubscriptionPlan $currentPlan, SubscriptionPlan $newPlan): float
    {
        $daysRemaining = now()->diffInDays(now()->endOfMonth(), false);
        $totalDays = now()->daysInMonth;

        $dailyRate = $currentPlan->monthly_price / $totalDays;
        $credit = round($dailyRate * $daysRemaining, 2);

        $newDailyRate = $newPlan->monthly_price / $totalDays;
        $remainingCost = round($newDailyRate * $daysRemaining, 2);

        if ($remainingCost > $credit) {
            return round($remainingCost - $credit, 2);
        }

        return 0;
    }
}
