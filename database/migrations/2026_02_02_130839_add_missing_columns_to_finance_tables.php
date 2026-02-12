<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Fix Wallets Table
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'balance')) {
                $table->decimal('balance', 15, 2)->default(0.00)->after('user_id');
            }
        });

        // 2. Fix Transactions Table
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'reference')) {
                $table->string('reference')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('transactions', 'description')) {
                $table->string('description')->nullable()->after('amount');
            }
            // Ensure status column exists or modify it if needed
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('completed'); 
            }
        });
    }

    public function down()
    {
        // Reverse if needed
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['reference', 'description']);
        });
    }
};