<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Chef, Food Truck, Market, Caterer, etc.
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actor_categories');
    }
};
