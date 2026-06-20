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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->bigInteger('user_creator_id');
            $table->string('address', 150);
            $table->string('address_number');
            $table->string('cp')->nullable();
            $table->string('identification_number')->nullable();
            $table->bigInteger('business_category_id')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('province_id')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->tinyInteger('is_subsidiary')->default(0);
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->tinyInteger('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
