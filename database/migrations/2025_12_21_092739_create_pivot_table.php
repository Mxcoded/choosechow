<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Links Chefs to Cuisines (Replacing the old 'specialties' and 'cuisines' JSON columns)
        Schema::create('chef_cuisine', function (Blueprint $table) {
            $table->foreignId('chef_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('cuisine_id')->constrained()->onDelete('cascade');
            $table->primary(['chef_profile_id', 'cuisine_id']);
        });

        // Links Menus to Cuisines
        Schema::create('cuisine_menu', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('cuisine_id')->constrained()->onDelete('cascade');
            $table->primary(['menu_id', 'cuisine_id']);
        });

        // Links Menus to Dietary Preferences
        Schema::create('dietary_preference_menu', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('dietary_preference_id')->constrained()->onDelete('cascade');
            $table->primary(['menu_id', 'dietary_preference_id'], 'dietary_menu_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dietary_preference_menu');
        Schema::dropIfExists('cuisine_menu');
        Schema::dropIfExists('chef_cuisine');
    }
};
