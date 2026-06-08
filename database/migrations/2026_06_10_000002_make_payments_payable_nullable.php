<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payable_type', 255)->nullable()->change();
            $table->unsignedBigInteger('payable_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payable_type', 255)->nullable(false)->change();
            $table->unsignedBigInteger('payable_id')->nullable(false)->change();
        });
    }
};
