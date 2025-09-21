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
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('boundaries')->nullable(); // Array of lat/lng coordinates
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('minimum_order_amount', 8, 2)->default(0);
            $table->integer('estimated_delivery_time')->default(30); // minutes
            $table->json('operating_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_zone');
    }
};
