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
        Schema::create('pix_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biker_id')->constrained('bikers');
            $table->string('key');
            $table->enum('type', [
                'cpf',
                'cnpj',
                'email',
                'phone',
                'other'
            ]);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pix_keys', function (Blueprint $table) {
            $table->dropForeign(['biker_id']);
        });
        Schema::dropIfExists('pix_keys');
    }
};
