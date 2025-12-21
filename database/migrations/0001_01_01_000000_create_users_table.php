<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->unique();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');

            // REMOVED: user_type (Moved to Roles & Permissions tables)

            // Status remains as it controls login ability (Banned vs Active)
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending_verification'])->default('pending_verification');

            $table->string('avatar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('referral_code', 10)->unique()->nullable();
            $table->string('referred_by')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('device_token')->nullable();
            $table->json('preferences')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('status'); // Optimized index
            $table->index('referral_code');
        });

        // (Keep password_reset_tokens and sessions schemas as they were...)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
