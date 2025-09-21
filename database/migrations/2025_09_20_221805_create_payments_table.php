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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('payable'); // orders, subscriptions, etc.
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('NGN');
            $table->enum('type', ['order_payment', 'subscription_payment', 'refund', 'payout']);
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method'); // card, bank_transfer, ussd, etc.
            $table->string('gateway')->default('paystack'); // paystack, flutterwave, etc.
            $table->string('gateway_reference')->nullable();
            $table->json('gateway_response')->nullable();
            $table->decimal('gateway_fee', 8, 2)->default(0);
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('gateway_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
