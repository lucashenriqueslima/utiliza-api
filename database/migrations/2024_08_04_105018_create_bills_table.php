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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_id')->constrained('calls');
            $table->decimal('value', 10, 2);
            $table->string('description')->nullable();
            $table->enum('status', ['pending', 'paid', 'canceled'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->enum('payment_method', ['pix'])->default('pix');
            $table->enum('pix_key_type', ['cpf', 'cnpj', 'email', 'phone', 'random'])->nullable();
            $table->string('pix_key')->nullable();
            $table->string('payment_vouncher_file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropConstrainedForeignId('call_id');
        });
        Schema::dropIfExists('bills_tables');
    }
};
