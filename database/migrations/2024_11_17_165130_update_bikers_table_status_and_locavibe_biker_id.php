<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bikers', function (Blueprint $table) {
            // Make locavibe_biker_id nullable
            $table->string('locavibe_biker_id')->nullable()->change();

            // Update the 'status' column to include new options and change default to 'pending_authenticator'
            $table->enum('status', [
                'avaible',
                'not_avaible',
                'busy',
                'banned',
                'inactive',
                'pending_authenticator'
            ])->default('pending_authenticator')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bikers', function (Blueprint $table) {
            // Revert locavibe_biker_id to non-nullable
            $table->string('locavibe_biker_id')->nullable(false)->change();

            // Revert the 'status' column to its previous state
            $table->enum('status', [
                'avaible',
                'not_avaible',
                'busy'
            ])->default('avaible')->change();
        });
    }
};
