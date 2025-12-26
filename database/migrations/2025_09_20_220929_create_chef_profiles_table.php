<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 1. Identity & Branding
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable(); // Optional: if you want specific business logo distinct from user avatar
            $table->integer('years_of_experience')->default(0);

            // 2. Location & Operations
            $table->string('kitchen_address'); // Required for delivery calculations
            $table->json('operating_hours')->nullable();
            $table->boolean('is_online')->default(true); // Matches Form Name (was accepts_orders)

            // 3. Order Settings
            $table->decimal('minimum_order', 10, 2)->default(0); // Matches Form Name (was minimum_order_amount)
            $table->integer('delivery_radius_km')->default(10);

            // 4. Financials (For receiving payouts)
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();

            // 5. System Status (Admin controlled or Cached)
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->boolean('is_featured')->default(false);

            // 6. Stats (Cached for performance)
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_orders')->default(0);

            $table->softDeletes();
            $table->timestamps();

            // Indexes for searching
            $table->index(['is_online', 'verification_status']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_profiles');
    }
};
