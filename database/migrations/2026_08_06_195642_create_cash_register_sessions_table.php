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
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            
            // Fondos
            $table->decimal('starting_cash', 10, 2)->default(0); // Fondo inicial
            $table->decimal('expected_cash', 10, 2)->nullable(); // Lo que debería haber (fondo inicial + ventas efectivo)
            $table->decimal('actual_cash', 10, 2)->nullable(); // Lo que contó el cajero físicamente
            $table->decimal('difference', 10, 2)->nullable(); // actual_cash - expected_cash (negativo es faltante, positivo sobrante)
            
            // Totales del turno
            $table->decimal('cash_sales', 10, 2)->default(0);
            $table->decimal('card_sales', 10, 2)->default(0);
            $table->decimal('transfer_sales', 10, 2)->default(0);
            $table->decimal('total_sales', 10, 2)->default(0); // Suma de las tres ventas
            
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_register_sessions');
    }
};
