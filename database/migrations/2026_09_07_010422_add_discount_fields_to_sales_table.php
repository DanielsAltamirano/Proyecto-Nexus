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
        Schema::table('sales', function (Blueprint $table) {
            // Precio unitario real cobrado (con descuento si aplica)
            $table->decimal('unit_price', 10, 2)->nullable()->after('total_price');
            // Precio unitario original antes del descuento (null si no hubo promo)
            $table->decimal('original_unit_price', 10, 2)->nullable()->after('unit_price');
            // Porcentaje de descuento aplicado (null si no hubo promo)
            $table->unsignedTinyInteger('discount_pct')->nullable()->after('original_unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'original_unit_price', 'discount_pct']);
        });
    }
};
