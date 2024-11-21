<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bikers', function (Blueprint $table) {
            $table->string('auth_token')->nullable()->after('status'); // Replace 'last_column_name' with the actual last column name in your table
            $table->boolean('auth_token_verified')->default(false)->after('auth_token');
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
            $table->dropColumn(['auth_token', 'auth_token_verified']);
        });
    }
};
