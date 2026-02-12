<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            // Only add 'city' if it doesn't exist
            if (!Schema::hasColumn('chef_profiles', 'city')) {
                $table->string('city')->nullable()->after('kitchen_address')->index();
            }

            // Only add 'state' if it doesn't exist
            if (!Schema::hasColumn('chef_profiles', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
        });
    }

    public function down()
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            $table->dropColumn(['city', 'state']);
        });
    }
};
