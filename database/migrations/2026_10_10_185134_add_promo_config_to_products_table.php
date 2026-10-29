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
            $table->json('promo_4x3_days')->nullable();
            $table->json('promo_4x3_shifts')->nullable();
            $table->json('promo_4x3_branches')->nullable();
            
            $table->json('promo_volumen_days')->nullable();
            $table->json('promo_volumen_shifts')->nullable();
            $table->json('promo_volumen_branches')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'promo_4x3_days', 'promo_4x3_shifts', 'promo_4x3_branches',
                'promo_volumen_days', 'promo_volumen_shifts', 'promo_volumen_branches'
            ]);
        });
    }
};
