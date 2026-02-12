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
            $table->string('order_number')->unique(); // e.g. ORD-2025-001
            
            // Who
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Customer
            $table->foreignId('chef_id')->constrained('users')->onDelete('cascade'); // Chef
            
            // Details
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, accepted, ready, completed, cancelled
            $table->string('payment_method')->default('cash'); // cash, transfer
            $table->string('payment_status')->default('pending'); // pending, paid
            
            // Logistics
            $table->text('delivery_address');
            $table->string('phone_number');
            $table->text('notes')->nullable(); // Allergies, etc.
            
            $table->timestamps();
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
