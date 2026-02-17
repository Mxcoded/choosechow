<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds any missing columns to chef_profiles table for consistency.
     */
    public function up(): void
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            // Add profile_image if it doesn't exist (distinct from cover_image)
            if (!Schema::hasColumn('chef_profiles', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('cover_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('chef_profiles', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });
    }
};
