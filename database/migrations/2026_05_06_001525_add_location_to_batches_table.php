<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Añadimos ubicación física. Nullable por si un lote llega pero aún no se acomoda.
            $table->string('rack')->nullable()->after('expiration_date');
            $table->string('shelf')->nullable()->after('rack')->comment('Stante en bodega');
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['rack', 'shelf']);
        });
    }
};