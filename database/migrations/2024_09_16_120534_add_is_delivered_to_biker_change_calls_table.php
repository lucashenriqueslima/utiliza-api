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
        Schema::table('biker_change_calls', function (Blueprint $table) {
            $table->boolean('is_delivered')->default(false)->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biker_change_calls', function (Blueprint $table) {
            $table->dropColumn('is_delivered');
        });
    }
};