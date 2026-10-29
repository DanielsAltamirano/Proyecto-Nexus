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
    Schema::create('batches', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        
        $table->string('batch_number')->comment('Número de lote del empaque');
        $table->integer('stock')->default(0);
        
        // Decimales exactos para dinero (10 dígitos en total, 2 decimales)
        $table->decimal('cost_price', 10, 2);
        $table->decimal('selling_price', 10, 2);
        
        // Fechas específicas
        $table->date('expiration_date');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
