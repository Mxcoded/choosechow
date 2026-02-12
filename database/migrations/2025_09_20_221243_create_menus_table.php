<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            // Ensure this is user_id, NOT chef_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->string('name');
            $table->string('slug')->nullable(); // Ensure Slug is here
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->string('category');
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
