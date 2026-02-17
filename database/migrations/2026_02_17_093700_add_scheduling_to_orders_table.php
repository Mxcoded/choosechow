<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Delivery scheduling
            $table->string('delivery_type')->default('asap')->after('notes'); // 'asap' or 'scheduled'
            $table->date('scheduled_date')->nullable()->after('delivery_type'); // Date for scheduled delivery
            $table->string('scheduled_time_slot')->nullable()->after('scheduled_date'); // e.g., '12:00-13:00', '18:00-19:00'
            $table->timestamp('scheduled_for')->nullable()->after('scheduled_time_slot'); // Full datetime for delivery
            
            // Additional delivery info
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('total_amount');
            $table->decimal('subtotal', 10, 2)->nullable()->after('delivery_fee'); // Items total before delivery fee
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_type',
                'scheduled_date',
                'scheduled_time_slot',
                'scheduled_for',
                'delivery_fee',
                'subtotal',
            ]);
        });
    }
};
