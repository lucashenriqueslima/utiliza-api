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
        Schema::create('third_party_cars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ileva_fipe_brand_id');
            $table->enum('vehicle_type', ['carro', 'moto', 'caminhao', 'outros']);
            $table->string('fipe_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_party_cars');
    }
};
