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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chef_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'out_for_delivery',
                'delivered',
                'cancelled',
                'refunded'
            ])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('service_fee', 8, 2)->default(0);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method'); // card, bank_transfer, wallet
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->json('delivery_address');
            $table->text('special_instructions')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['chef_id', 'status']);
            $table->index('order_number');
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
