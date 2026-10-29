<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 1. Agrega 'cedula_medica' y 'status' (Solicitada -> Validación -> Surtida) a 'sales'.
     * 2. Crea la tabla relacional 'audit_outboxes' para el Patrón Outbox hacia MongoDB.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'cedula_medica')) {
                $table->string('cedula_medica', 50)->nullable()->after('has_receta_completa');
            }
            if (!Schema::hasColumn('sales', 'status')) {
                $table->string('status', 30)->default('Surtida')->after('cedula_medica');
            }
        });

        if (!Schema::hasTable('audit_outboxes')) {
            Schema::create('audit_outboxes', function (Blueprint $table) {
                $table->id();
                $table->string('idempotency_key', 120)->unique();
                $table->unsignedBigInteger('sale_id')->nullable()->index();
                $table->string('receipt_id', 50)->nullable()->index();
                $table->string('collection', 60)->default('audit_logs');
                $table->string('event_type', 60)->default('sale.audit');
                $table->json('payload');
                $table->string('status', 20)->default('pending')->index(); // pending, synced, failed
                $table->unsignedInteger('attempts')->default(0);
                $table->text('last_error')->nullable();
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_outboxes');

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('sales', 'cedula_medica')) {
                $table->dropColumn('cedula_medica');
            }
        });
    }
};

