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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biker_id')->nullable()->constrained('bikers');
            $table->foreignId('associate_car_id')->constrained('associate_cars');
            $table->bigInteger('ileva_associate_vehicle_id');
            $table->geometry('location', subtype: 'point')->nullable();
            $table->enum('status', ['waiting_biker', 'in_service', 'done'])->default('waiting_biker');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->dropForeign(['biker_id']);
            $table->dropForeign(['associate_id']);
        });
        Schema::dropIfExists('calls');
    }
};
