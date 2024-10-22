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
        Schema::table('expertises', function (Blueprint $table) {
            // Modify the 'type' column to add 'general' to the enum options
            $table->enum('type', ['main', 'secondary', 'general'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expertises', function (Blueprint $table) {
            // Reverse the change by removing 'general' from the enum options
            $table->enum('type', ['main', 'secondary'])->change();
        });
    }
};
