<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'cancelled', 'expired', 'trial', 'past_due'])->default('trial');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('amount', 8, 2);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start');
            $table->timestamp('current_period_end');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('paystack_subscription_code')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('paystack_subscription_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
