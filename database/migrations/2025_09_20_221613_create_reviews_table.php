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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chef_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('menu_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->json('rating_breakdown')->nullable(); // Food quality, delivery time, etc.
            $table->json('images')->nullable(); // Customer photos
            $table->boolean('is_verified')->default(true); // From actual order
            $table->boolean('is_featured')->default(false);
            $table->text('chef_response')->nullable();
            $table->timestamp('chef_responded_at')->nullable();
            $table->timestamps();

            $table->unique('order_id'); // One review per order
            $table->index(['chef_id', 'rating']);
            $table->index(['menu_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reviews');
    }
};
