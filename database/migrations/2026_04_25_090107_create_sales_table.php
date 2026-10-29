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
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        
        // Relaciones
        $table->foreignId('product_id')->constrained()->onDelete('restrict');
        $table->foreignId('batch_id')->constrained()->onDelete('restrict');
        $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Quién hizo la venta
        
        // Detalles de la salida
        $table->integer('quantity');
        $table->decimal('total_price', 10, 2); // Precio al momento de la venta
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
