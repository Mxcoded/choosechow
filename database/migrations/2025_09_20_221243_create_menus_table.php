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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chef_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->decimal('discounted_price', 8, 2)->nullable();
            $table->string('category'); // Main course, appetizer, dessert, etc.
            $table->json('cuisine_types'); // Nigerian, Continental, etc.
            $table->json('dietary_info')->nullable(); // Vegetarian, vegan, gluten-free, etc.
            $table->integer('preparation_time_minutes')->nullable();
            $table->integer('serves_count')->default(1);
            $table->json('ingredients')->nullable();
            $table->json('allergens')->nullable();
            $table->json('nutritional_info')->nullable();
            $table->text('cooking_instructions')->nullable();
            $table->text('storage_instructions')->nullable();
            $table->string('spice_level')->nullable(); // Mild, medium, hot
            $table->json('images'); // Array of image URLs
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->date('featured_until')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->integer('stock_quantity')->nullable(); // For limited items
            $table->json('availability_schedule')->nullable(); // When this item is available
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('order_count')->default(0);
            $table->timestamps();

            $table->unique(['chef_id', 'slug']);
            $table->index(['chef_id', 'is_available']);
            $table->index(['category', 'is_available']);
            $table->index('average_rating');
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
