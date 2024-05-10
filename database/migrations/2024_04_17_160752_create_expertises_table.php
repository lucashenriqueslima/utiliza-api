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
        Schema::create('expertises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained('calls');
            $table->integer('app_expertise_index');
            $table->enum('type', ['main', 'final']);
            $table->enum('status', ['canceled', 'done', 'waiting'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expertises', function (Blueprint $table) {
            $table->dropConstrainedForeignId('call_id');
        });
        Schema::dropIfExists('expertises');
    }
};
