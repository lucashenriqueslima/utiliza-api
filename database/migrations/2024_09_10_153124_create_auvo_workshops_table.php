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
        Schema::create('auvo_workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auvo_collaborator_id')->constrained()->cascadeOnDelete();
            $table->integer('ileva_id')->unique();
            $table->string('visit_time');
            $table->json('days_of_week');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auvo_workshops', function (Blueprint $table) {
            $table->dropConstrainedForeignId('auvo_collaborator_id');
        });
        Schema::dropIfExists('auvo_workshops');
    }
};
