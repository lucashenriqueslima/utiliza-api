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
        Schema::create('expertise_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expertise_id')->constrained('expertises');
            $table->integer('fileable_id');
            $table->string('fileable_type');
            $table->string('description')->nullable();
            $table->string('path');
            $table->enum('image_type', ['plate', 'front_side', 'right_side', 'left_side', 'rear_side', 'street', 'others'])->nullable();
            $table->enum('file_type', ['audio', 'image', 'video']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expertise_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expertise_id');
        });
        Schema::dropIfExists('expertise_files');
    }
};
