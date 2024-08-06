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
            $table->enum('type', ['cpf', 'cnpj', 'email', 'phone', 'random']);
            $table->boolean('is_valid')->default(true);
            $table->string('bank');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pix_keys');
    }
};
