<?php

namespace App\Console\Commands;

use App\Models\UserSubscription;
use App\Models\User;
use App\Services\BillingService;
use App\Services\DiscountService;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionRenewals extends Command
{
    protected $signature = 'subscriptions:renew';
    protected $description = 'Process subscription renewals, retries, and grace period expirations';

    public function __construct(
        protected BillingService $billingService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting subscription renewal processing...');

        $this->processDueRenewals();
        $this->processRetries();
        $this->processGracePeriods();
        $this->processDedicatedRiderAssignments();

        $this->info('Subscription renewal processing complete.');

        return Command::SUCCESS;
    }

    protected function processDueRenewals(): void
    {
        $dueSubscriptions = UserSubscription::whereIn('status', ['active'])
            ->where('current_period_end', '<=', now())
            ->get();

        $count = 0;
        foreach ($dueSubscriptions as $subscription) {
            try {
                $result = $this->billingService->processRenewal($subscription);

                if ($result['success']) {
                    $this->info("Renewal initiated for subscription #{$subscription->id}");
                } else {
                    $this->warn("Renewal failed for subscription #{$subscription->id}: {$result['message']}");
                }
            } catch (\Exception $e) {
                Log::error("Subscription renewal error for #{$subscription->id}: {$e->getMessage()}");
                $this->error("Error renewing subscription #{$subscription->id}: {$e->getMessage()}");
            }
            $count++;
        }

        $this->info("Processed {$count} due renewals.");
    }

    protected function processRetries(): void
    {
        $retrySubscriptions = UserSubscription::whereIn('status', ['active', 'past_due'])
            ->where('current_period_end', '<=', now()->addDays(3))
            ->get()
            ->filter(fn($s) => $this->billingService->shouldRetry($s));

        $count = 0;
        foreach ($retrySubscriptions as $subscription) {
            try {
                $result = $this->billingService->processRenewal($subscription);
                if ($result['success']) {
                    $this->info("Retry succeeded for subscription #{$subscription->id}");
                } else {
                    $this->warn("Retry failed for subscription #{$subscription->id}");
                }
            } catch (\Exception $e) {
                Log::error("Retry error for subscription #{$subscription->id}: {$e->getMessage()}");
            }
            $count++;
        }

        $this->info("Processed {$count} payment retries.");
    }

    protected function processGracePeriods(): void
    {
        $pastDueUsers = User::where('subscription_status', 'past_due')->get();

        $count = 0;
        foreach ($pastDueUsers as $user) {
            try {
                $this->billingService->applyGracePeriod($user);
                if ($user->subscription_status === 'expired') {
                    $this->info("Subscription expired for user #{$user->id}");
                }
            } catch (\Exception $e) {
                Log::error("Grace period error for user #{$user->id}: {$e->getMessage()}");
            }
            $count++;
        }

        $this->info("Processed {$count} grace period checks.");
    }

    protected function processDedicatedRiderAssignments(): void
    {
        $premiumUsers = User::where('subscription_tier', 'premium')
            ->where('subscription_status', 'active')
            ->whereNull('dedicated_rider_id')
            ->get();

        $rider = User::role('rider')->inRandomOrder()->first();

        if (!$rider) {
            $this->warn('No riders available for assignment.');
            return;
        }

        $count = 0;
        foreach ($premiumUsers as $user) {
            $user->update(['dedicated_rider_id' => $rider->id]);
            $this->info("Assigned rider #{$rider->id} to user #{$user->id}");
            $count++;
        }

        $this->info("Assigned riders to {$count} Premium users.");
    }
}
