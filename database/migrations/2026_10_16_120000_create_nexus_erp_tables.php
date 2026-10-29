<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Nexus ERP specifications.
     */
    public function up(): void
    {
        // 1. Tabla de Clientes (CRM)
        if (!Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('empresa')->nullable();
                $table->string('telefono')->nullable();
                $table->string('email')->nullable();
                $table->text('direccion')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Extensión para Proveedores (razon_social, rfc)
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'razon_social')) {
                $table->string('razon_social')->nullable()->after('name');
            }
            if (!Schema::hasColumn('providers', 'rfc')) {
                $table->string('rfc', 20)->nullable()->after('razon_social');
            }
        });

        // 3. Extensión para Productos (SoftDeletes para borrado lógico y precio base)
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('name');
            }
        });

        // 4. Tabla de Gastos Operativos (Finanzas)
        if (!Schema::hasTable('operational_expenses')) {
            Schema::create('operational_expenses', function (Blueprint $table) {
                $table->id();
                $table->string('concepto');
                $table->decimal('monto', 12, 2);
                $table->date('fecha')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_expenses');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }
        });

        Schema::table('providers', function (Blueprint $table) {
            if (Schema::hasColumn('providers', 'razon_social')) {
                $table->dropColumn('razon_social');
            }
            if (Schema::hasColumn('providers', 'rfc')) {
                $table->dropColumn('rfc');
            }
        });

        Schema::dropIfExists('clients');
    }
};
