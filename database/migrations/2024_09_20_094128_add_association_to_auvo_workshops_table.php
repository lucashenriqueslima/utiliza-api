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
        Schema::table('auvo_workshops', function (Blueprint $table) {
            $table->enum('association', ['solidy', 'nova', 'motoclub'])->after('auvo_collaborator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auvo_workshops', function (Blueprint $table) {
            //
        });
    }
};
