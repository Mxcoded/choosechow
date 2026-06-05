<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('subscription_tier')->default('none')->after('preferences');
            $table->string('subscription_status')->default('none')->after('subscription_tier');
            $table->timestamp('renews_at')->nullable()->after('subscription_status');
            $table->integer('free_delivery_used')->default(0)->after('renews_at');
            $table->unsignedBigInteger('dedicated_rider_id')->nullable()->after('free_delivery_used');

            $table->index(['subscription_tier', 'subscription_status']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_tier',
                'subscription_status',
                'renews_at',
                'free_delivery_used',
                'dedicated_rider_id',
            ]);
        });
    }
};
