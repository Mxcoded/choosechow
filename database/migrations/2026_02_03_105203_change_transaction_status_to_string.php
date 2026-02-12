<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Force the column to be a simple text field (VARCHAR)
        // This accepts 'completed', 'successful', 'success', etc.
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'completed'");
    }

    public function down()
    {
        // No need to reverse this in a fix
    }
};