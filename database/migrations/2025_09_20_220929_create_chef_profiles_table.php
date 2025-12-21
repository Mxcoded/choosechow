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
        Schema::create('chef_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->text('bio');

            // REMOVED: specialties, cuisines (Moved to Pivot Tables)

            $table->integer('years_of_experience')->default(0);
            $table->string('kitchen_address');
            $table->decimal('kitchen_latitude', 10, 8)->nullable();
            $table->decimal('kitchen_longitude', 11, 8)->nullable();
            $table->integer('delivery_radius_km')->default(10);
            $table->decimal('minimum_order_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->boolean('free_delivery_over_amount')->default(false);
            $table->decimal('free_delivery_threshold', 10, 2)->nullable();
            $table->json('operating_hours'); // Keep this as JSON, it's structural config
            $table->boolean('accepts_orders')->default(true);
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->json('certifications')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('bvn')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();

            $table->softDeletes(); // ADDED: Soft Deletes
            $table->timestamps();

            $table->index(['verification_status', 'accepts_orders']);
            $table->index(['kitchen_latitude', 'kitchen_longitude']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chef_profiles');
    }
};
