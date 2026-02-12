<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Fix Transactions Table
        Schema::table('transactions', function (Blueprint $table) {
            // Check if column exists before adding to avoid duplicate errors
            if (!Schema::hasColumn('transactions', 'reference')) {
                $table->string('reference')->unique()->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('transactions', 'description')) {
                $table->string('description')->nullable()->after('amount');
            }
            // If you need to change 'status' values
            // Note: modifying enums is tricky, we usually just add the column if missing
        });

        // 2. Fix Wallets Table
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'balance')) {
                $table->decimal('balance', 15, 2)->default(0.00)->after('user_id');
            }
        });
    }

    public function down()
    {
        // Reverse the changes if needed
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['reference', 'description']);
        });
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['balance']);
        });
    }
};