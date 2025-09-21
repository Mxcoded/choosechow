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
        Schema::create('chef_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_id')->constrained('users')->onDelete('cascade');
            $table->string('payout_reference')->unique();
            $table->decimal('gross_amount', 12, 2); // Total order amount
            $table->decimal('commission_amount', 10, 2); // Platform commission
            $table->decimal('net_amount', 12, 2); // Amount to pay chef
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->enum('payout_method', ['bank_transfer', 'wallet'])->default('bank_transfer');
            $table->json('bank_details')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->json('orders_included'); // Array of order IDs
            $table->date('payout_period_start');
            $table->date('payout_period_end');
            $table->timestamps();

            $table->index(['chef_id', 'status']);
            $table->index('payout_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chef_payouts');
    }
};
