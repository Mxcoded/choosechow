<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('order_priority')->default(0)->after('notes');
            $table->decimal('delivery_fee_override', 10, 2)->nullable()->after('order_priority');
            $table->decimal('discount_applied', 10, 2)->default(0)->after('delivery_fee_override');
            $table->unsignedBigInteger('assigned_rider_id')->nullable()->after('discount_applied');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_priority',
                'delivery_fee_override',
                'discount_applied',
                'assigned_rider_id',
            ]);
        });
    }
};
