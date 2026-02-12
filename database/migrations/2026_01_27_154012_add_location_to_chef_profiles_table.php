<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            // Add city and state after the bio column
            $table->string('city')->nullable()->after('bio');
            $table->string('state')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            $table->dropColumn(['city', 'state']);
        });
    }
};
