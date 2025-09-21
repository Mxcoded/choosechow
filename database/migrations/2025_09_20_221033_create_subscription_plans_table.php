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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('user_type', ['customer', 'chef']);
            $table->text('description');
            $table->decimal('monthly_price', 8, 2);
            $table->decimal('yearly_price', 8, 2);
            $table->decimal('commission_rate', 5, 2)->nullable(); // For chef plans
            $table->integer('menu_items_limit')->nullable(); // For chef plans
            $table->json('features'); // Array of features
            $table->json('limits')->nullable(); // Various limits
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('trial_days')->default(7);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
