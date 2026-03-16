<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waitlist_signup_id')->constrained()->onDelete('cascade');
            
            // What food do they want?
            $table->json('favorite_meals')->nullable();       // Array of meal names/types
            
            // Dietary preferences (links to existing dietary_preferences table via JSON IDs or names)
            $table->json('dietary_preferences')->nullable();  // Array: ["Vegan", "Keto", "Halal"]
            
            // Why ChooseChow over fast food?
            $table->text('reason_for_choosing')->nullable();
            
            // Additional insights
            $table->string('preferred_price_range')->nullable(); // budget, mid-range, premium
            $table->integer('meals_per_week')->nullable();       // How often would they order
            $table->json('preferred_cuisines')->nullable();      // Links to cuisines table
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist_surveys');
    }
};
