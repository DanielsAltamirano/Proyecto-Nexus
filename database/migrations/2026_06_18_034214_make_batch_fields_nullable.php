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
        Schema::table('batches', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->comment('Número de lote del empaque (opcional para existencias generales)')->change();
            $table->date('expiration_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->string('batch_number')->nullable(false)->change();
            $table->date('expiration_date')->nullable(false)->change();
        });
    }
};
