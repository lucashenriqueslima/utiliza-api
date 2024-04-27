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
        Schema::create('expertise_form_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expertise_id')->constrained('expertises');
            $table->foreignId('expertise_file_id')->nullable()->constrained('expertise_files');
            $table->integer('inputable_id');
            $table->string('inputable_type');
            $table->enum('field', ['associate_report_text',
                'associate_report_audio',
                'associate_report_video',
                'associate_street_image',
                'associate_plate_image',
                'associate_front_side',
                'associate_right_side',
                'associate_left_side',
                'associate_rear_side',
                'third_party_name',
                'third_party_cpf',
                'third_party_phone',
                'third_party_plate',
                'third_party_chassi',
                'third_party_renavam',
                'third_party_brand',
                'third_party_model',
                'third_party_year',
                'third_party_color',
                'third_party_street_image',
                'third_party_plate_image',
                'third_party_front_side',
                'third_party_right_side',
                'third_party_left_side',
                'third_party_rear_side',
                'third_party_report_text',
                'third_party_report_audio',
                'third_party_report_video',
                'street_video',
                'biker_observation',
        ]);
            $table->enum('input_type', ['text', 'file', 'select']);
            $table->enum('status', ['approved', 'refused'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expertise_form_inputs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expertise_id');
            $table->dropConstrainedForeignId('expertise_file_id');
        });
        Schema::dropIfExists('expertise_form_inputs');
    }
};
