<<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Fix Transactions (Add reference & description)
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'reference')) {
                $table->string('reference')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('transactions', 'description')) {
                $table->string('description')->nullable()->after('amount');
            }
            // Ensure status exists
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('completed'); 
            }
        });

        // 2. Fix Wallets (Add balance)
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasColumn('wallets', 'balance')) {
                $table->decimal('balance', 15, 2)->default(0.00)->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
