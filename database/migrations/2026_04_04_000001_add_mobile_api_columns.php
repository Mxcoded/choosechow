<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds columns needed for the Mobile App API.
     */
    public function up(): void
    {
        // Add special_instructions to carts table
        Schema::table('carts', function (Blueprint $table) {
            if (!Schema::hasColumn('carts', 'special_instructions')) {
                $table->text('special_instructions')->nullable()->after('quantity');
            }
        });

        // Add special_instructions to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'special_instructions')) {
                $table->text('special_instructions')->nullable()->after('quantity');
            }
            // Add 'name' column if it doesn't exist (alias for menu_name)
            if (!Schema::hasColumn('order_items', 'name') && Schema::hasColumn('order_items', 'menu_name')) {
                // We'll handle this in the model instead
            }
        });

        // Add user_id, title, message to notifications table for simpler API queries
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index('user_id');
            }
            if (!Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->nullable()->after('type');
            }
            if (!Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable()->after('title');
            }
        });

        // Add order_id to payments table for direct order relationship
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->after('user_id');
                $table->index('order_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'special_instructions')) {
                $table->dropColumn('special_instructions');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'special_instructions')) {
                $table->dropColumn('special_instructions');
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('notifications', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('notifications', 'message')) {
                $table->dropColumn('message');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'order_id')) {
                $table->dropIndex(['order_id']);
                $table->dropColumn('order_id');
            }
        });
    }
};
