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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_promo_volumen')->default(false)->after('is_promo_4x3');
            $table->integer('promo_volumen_min_qty')->nullable()->after('is_promo_volumen');
            $table->decimal('promo_volumen_price', 10, 2)->nullable()->after('promo_volumen_min_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_promo_volumen', 'promo_volumen_min_qty', 'promo_volumen_price']);
        });
    }
};
