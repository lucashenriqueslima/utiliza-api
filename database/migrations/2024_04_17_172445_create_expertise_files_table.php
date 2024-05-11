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
            $table->string('path');
            $table->enum('file_expertise_type', [
                'report_audio',
                'report_video',
                'plate_image',
                'front_side_image',
                'right_side_image',
                'left_side_image',
                'rear_side_image',
                'street_video',
            ]);
            $table->enum('file_type', ['audio', 'image', 'video']);
            $table->enum('status', ['approved', 'refused'])->nullable();
            $table->string('refusal_description')->nullable();
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
