<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Añadimos el tipo (GEN o PAT) después del nombre. 
            // Usamos string corto para optimizar el peso en base de datos.
            $table->string('type', 10)->default('GEN')->after('name')->comment('GEN = Genérico, PAT = Patente');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};