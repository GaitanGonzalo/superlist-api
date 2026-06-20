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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('name', 150);
            $table->string('identification_number', 20)->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('razon_social', 150)->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->string('address', 255)->nullable();
            $table->integer('address_number')->nullable();
            $table->boolean('is_subsidiary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
