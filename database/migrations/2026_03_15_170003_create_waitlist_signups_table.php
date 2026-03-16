<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist_signups', function (Blueprint $table) {
            $table->id();
            
            // Core user data
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            
            // Role: food_lover (demand) or vendor (supply)
            $table->enum('role', ['food_lover', 'vendor'])->default('food_lover');
            
            // Location
            $table->foreignId('neighborhood_id')->nullable()->constrained()->nullOnDelete();
            
            // Vendor-specific (nullable for food lovers)
            $table->foreignId('actor_category_id')->nullable()->constrained()->nullOnDelete();
            
            // Referral system
            $table->string('referral_token', 10)->unique();
            $table->foreignId('referred_by_id')->nullable()
                ->references('id')->on('waitlist_signups')->nullOnDelete();
            
            // UTM tracking
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            
            // Manual discovery (if no UTM)
            $table->string('discovery_source')->nullable(); // social_media, friend, search, advertisement, other
            
            // Tracking metadata
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // Progress tracking
            $table->tinyInteger('step_completed')->default(1); // 1 = signup, 2 = survey completed
            
            // Status
            $table->enum('status', ['pending', 'verified', 'converted', 'unsubscribed'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('converted_at')->nullable(); // When they become actual users
            
            $table->timestamps();

            // Indexes for analytics
            $table->index('role');
            $table->index('neighborhood_id');
            $table->index('utm_source');
            $table->index('referred_by_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist_signups');
    }
};
