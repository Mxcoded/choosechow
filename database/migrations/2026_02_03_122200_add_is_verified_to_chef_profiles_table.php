<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('chef_profiles', 'is_verified')) {
                // Add column after business_name for visibility
                $table->boolean('is_verified')->default(false)->after('business_name');
            }
        });
    }

    public function down()
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
};