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
        Schema::create('biker_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biker_id')->constrained('bikers');
            $table->string('device_id');
            $table->string('device_name');
            $table->string('device_model');
            $table->boolean('is_active')->default(true);
            $table->timestamp('came_out_in')->nullable();
            $table->timestamps();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biker_accesses');
    }
};
