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
        Schema::create('accident_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accident_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->enum('type', [
                'front',
                'front_left',
                'front_right',
                'rear',
                'rear_left',
                'rear_right',
                'left',
                'right',
                'trunk',
                'trunk_tire',
                'dashboard',
                'crlv',
                'cnh',
            ]);
            $table->boolean('is_current')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accident_images');
    }
};
