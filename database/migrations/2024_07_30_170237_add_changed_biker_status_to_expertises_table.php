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
            $table->enum('status', ['canceled', 'done', 'waiting', 'waiting_validate_from_others', 'changed_biker'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expertises', function (Blueprint $table) {
            $table->enum('status', ['canceled', 'done', 'waiting', 'waiting_validate_from_others'])->nullable()->change();
        });
    }
};
