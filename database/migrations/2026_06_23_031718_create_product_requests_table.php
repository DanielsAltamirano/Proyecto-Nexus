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
        Schema::create('product_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete(); // Sucursal que pide
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Quien lo pidió
            $table->integer('quantity');
            $table->string('status')->default('pending'); // pending, partial_bodega, ordered_to_provider, fulfilled, rejected
            $table->text('notes')->nullable();
            
            // Relaciones opcionales para saber cómo se surtió
            $table->unsignedBigInteger('transfer_id')->nullable(); // Si se surtió por bodega
            $table->unsignedBigInteger('purchase_order_id')->nullable(); // Si se compró

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_requests');
    }
};
