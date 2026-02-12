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
            $table->decimal('delivery_fee', 10, 2)->default(1500.00)->after('business_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chef_profiles', function (Blueprint $table) {
            //
        });
    }
};
