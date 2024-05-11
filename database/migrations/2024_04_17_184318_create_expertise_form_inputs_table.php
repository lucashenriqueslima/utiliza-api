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
            $table->enum('field_type', [
                'report_text',
                'name',
                'cpf',
                'phone',
                'plate',
                'chassi',
                'renavam',
                'brand',
                'model',
                'year',
                'color',
                'biker_observation',
            ]);
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
        Schema::table('expertise_form_inputs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('expertise_id');
            $table->dropConstrainedForeignId('expertise_file_id');
        });
        Schema::dropIfExists('expertise_form_inputs');
    }
};
