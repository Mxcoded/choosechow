<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verify users with status='active' but no email_verified_at timestamp
        DB::table('users')
            ->where('status', 'active')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('NOW()')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data migration - reversing would lose data
        // So we make it a no-op
    }
};
