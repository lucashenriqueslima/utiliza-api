<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiredStatusToExpertiseFileValidationErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('expertise_file_validation_errors', function (Blueprint $table) {
            $table->enum('status', ['pending', 'sent', 'read', 'expired'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('expertise_file_validation_errors', function (Blueprint $table) {
            $table->enum('status', ['pending', 'sent', 'read'])->nullable()->change();
        });
    }
}
