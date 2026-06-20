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
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('store_name')->nullable();
            $table->string('store_address')->nullable();
            $table->tinyInteger('is_finished')->default(0);
            $table->timestamp('finished_at')->nullable();
            $table->decimal('total_spent', 10,2);
            $table->decimal('actual_total', 10,2);
             $table->tinyInteger('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_lists');
    }
};
