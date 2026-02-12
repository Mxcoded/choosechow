<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert Default Settings
        DB::table('system_settings')->insert([
            ['key' => 'site_name', 'value' => 'ChooseChow'],
            ['key' => 'commission_fee', 'value' => '5'], // 5%
            ['key' => 'support_email', 'value' => 'support@choosechow.com'],
            ['key' => 'support_phone', 'value' => '0800-CHOOSE-CHOW'],
            ['key' => 'min_withdrawal', 'value' => '5000'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};