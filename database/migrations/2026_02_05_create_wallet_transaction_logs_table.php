<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates a wallet_transaction_logs table to maintain complete audit trail
     * of all wallet balance changes. Each entry records:
     * - The user whose wallet was affected
     * - The transaction type (earning, payout, refund, adjustment)
     * - The amount changed
     * - Balance before and after
     * - Reference to the related transaction or order
     * - Timestamp of when the change occurred
     */
    public function up(): void
    {
        Schema::create('wallet_transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['earning', 'payout', 'refund', 'adjustment'])->comment('Type of wallet transaction');
            $table->decimal('amount', 15, 2)->comment('Amount changed');
            $table->decimal('balance_before', 15, 2)->comment('Wallet balance before transaction');
            $table->decimal('balance_after', 15, 2)->comment('Wallet balance after transaction');
            $table->string('reference')->nullable()->comment('Reference to order, payment, or withdrawal ID');
            $table->text('description')->nullable()->comment('Human-readable description of the transaction');
            $table->timestamps();

            // Indexes for fast querying
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('type');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transaction_logs');
    }
};
