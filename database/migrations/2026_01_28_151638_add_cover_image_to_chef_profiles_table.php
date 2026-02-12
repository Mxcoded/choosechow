<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('kitchen_address');
        });
    }

    public function down()
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
