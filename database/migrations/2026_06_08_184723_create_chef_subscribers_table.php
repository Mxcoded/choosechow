<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chef_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('chef_id')->constrained('users')->onDelete('cascade');
            $table->boolean('notify_new_menu')->default(true);
            $table->boolean('notify_promotions')->default(true);
            $table->boolean('notify_availability')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'chef_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chef_subscribers');
    }
};
