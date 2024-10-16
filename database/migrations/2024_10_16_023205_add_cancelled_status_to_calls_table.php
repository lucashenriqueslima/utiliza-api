<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelledStatusToCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->enum('status', [
                'searching_biker',
                'waiting_arrival',
                'in_service',
                'waiting_validation',
                'in_validation',
                'waiting_biker_see_validation',
                'approved',
                'cancelled' // Adicionando o status "cancelled"
            ])->default('searching_biker')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->enum('status', [
                'searching_biker',
                'waiting_arrival',
                'in_service',
                'waiting_validation',
                'in_validation',
                'waiting_biker_see_validation',
                'approved'
            ])->default('searching_biker')->change();
        });
    }
}
