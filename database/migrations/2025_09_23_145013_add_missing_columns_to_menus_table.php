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
        Schema::table('menus', function (Blueprint $table) {
            $table->json('nutritional_info')->nullable()->after('allergens');
            $table->text('cooking_instructions')->nullable()->after('nutritional_info');
            $table->text('storage_instructions')->nullable()->after('cooking_instructions');
            $table->date('featured_until')->nullable()->after('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['nutritional_info', 'cooking_instructions', 'storage_instructions', 'featured_until']);
        });
    }
};
