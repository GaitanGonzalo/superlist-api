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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //SAL
            $table->string('ean_code', 20);
            $table->bigInteger('user_creator_id')->default(1);
            $table->bigInteger('brand_id')->nullable(); //marca
            $table->bigInteger('products_category_id')->nullable();
            $table->string('rubro_id')->nullable();
            $table->string('caracteristics')->nullable(); //marina | Himalaya | repostero
            $table->integer('weight')->default(0); //peso
            $table->string('um')->nullable();
            $table->tinyInteger('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
