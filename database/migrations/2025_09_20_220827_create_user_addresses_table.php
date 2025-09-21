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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('delivery'); // delivery, billing, pickup
            $table->string('label')->nullable(); // Home, Office, etc.
            $table->string('street_address');
            $table->string('apartment')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Nigeria');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
