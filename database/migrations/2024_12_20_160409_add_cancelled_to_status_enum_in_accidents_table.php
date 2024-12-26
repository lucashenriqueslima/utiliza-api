<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Modify the `status` enum to include 'cancelled' in the `accidents` table
        DB::statement("ALTER TABLE accidents MODIFY COLUMN status ENUM('pending', 'in_progress', 'finished', 'cancelled')");
    }

    public function down()
    {
        // Revert the `status` enum to remove 'cancelled' from the `accidents` table
        DB::statement("ALTER TABLE accidents MODIFY COLUMN status ENUM('pending', 'in_progress', 'finished')");
    }
};
