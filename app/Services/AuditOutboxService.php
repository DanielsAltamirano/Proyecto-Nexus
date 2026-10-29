<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\AuditOutbox;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuditOutboxService
{
    /**
     * Registra en MongoDB (AuditLog) la bitácora inmutable de la venta / receta médica.
     * Implementa el Patrón Outbox con clave de idempotencia:
     * Si MongoDB falla, persiste el evento en la tabla relacional 'audit_outboxes'
     * sin abortar ni corromper la transacción de venta en MySQL.
     */
    public static function recordSaleAudit(Sale $sale, ?Product $product = null, array $extra = []): void
    {
        $product = $product ?? $sale->product;
        $isControlled = (bool) ($product?->is_controlled ?? false);
        $idempotencyKey = sprintf('sale_%d_%s', $sale->id, $sale->receipt_id ?: 'noreceipt');

        $statusHistory = $extra['status_history'] ?? (
            $isControlled
                ? [Sale::STATUS_SOLICITADA, Sale::STATUS_VALIDACION, Sale::STATUS_SURTIDA]
                : [Sale::STATUS_SOLICITADA, Sale::STATUS_SURTIDA]
        );

        $payload = [
            'idempotency_key'   => $idempotencyKey,
            'event_type'        => $isControlled ? 'controlled_medication_sale' : 'standard_sale',
            'sale_id'           => $sale->id,
            'receipt_id'        => $sale->receipt_id,
            'product_id'        => $sale->product_id,
            'product_name'      => $product?->name ?? 'Servicio / Producto General',
            'sku'               => $product?->sku,
            'is_controlled'     => $isControlled,
            'cedula_medica'     => $sale->cedula_medica,
            'quantity'          => (int) $sale->quantity,
            'unit_price'        => (float) $sale->unit_price,
            'total_price'       => (float) $sale->total_price,
            'status'            => $sale->status ?? Sale::STATUS_SURTIDA,
            'status_history'    => $statusHistory,
            'user_id'           => $sale->user_id,
            'branch_id'         => $extra['branch_id'] ?? $sale->batch?->branch_id,
            'client_id'         => $sale->client_id,
            'prescription_data' => $isControlled ? [
                'cedula_medica'         => $sale->cedula_medica,
                'prescription_required' => (bool) ($product?->prescription_required ?? true),
                'active_ingredient'     => $product?->active_ingredient,
                'presentation'          => $product?->presentation,
                'validated_at'          => now()->toIso8601String(),
            ] : null,
            'metadata'          => [
                'payment_method' => $sale->payment_method,
                'batch_id'       => $sale->batch_id,
            ],
            'recorded_at'       => now()->toIso8601String(),
        ];

        try {
            if (!extension_loaded('mongodb') && !class_exists(\MongoDB\Client::class)) {
                throw new \RuntimeException('Driver ext-mongodb o cliente MongoDB no disponible en este entorno.');
            }

            // Operación idempotente: verificar si ya existe en MongoDB antes de insertar
            $existing = AuditLog::where('idempotency_key', $idempotencyKey)->first();
            if (!$existing) {
                AuditLog::create($payload);
            }

            // Si existía un registro pendiente en el Outbox relacional, marcarlo como sincronizado
            AuditOutbox::where('idempotency_key', $idempotencyKey)
                ->where('status', '!=', 'synced')
                ->update([
                    'status'     => 'synced',
                    'synced_at'  => now(),
                    'last_error' => null,
                ]);
        } catch (Throwable $e) {
            Log::warning("Fallo al insertar AuditLog en MongoDB para venta #{$sale->id}. Guardando en Outbox relacional.", [
                'idempotency_key' => $idempotencyKey,
                'error'           => $e->getMessage(),
            ]);

            // Guardar o actualizar de forma idempotente en la tabla relacional temporal (MySQL)
            $outbox = AuditOutbox::firstOrNew(['idempotency_key' => $idempotencyKey]);
            $outbox->fill([
                'sale_id'    => $sale->id,
                'receipt_id' => $sale->receipt_id,
                'collection' => 'audit_logs',
                'event_type' => $payload['event_type'],
                'payload'    => $payload,
                'status'     => 'pending',
                'attempts'   => ((int) $outbox->attempts) + 1,
                'last_error' => substr($e->getMessage(), 0, 1000),
            ]);
            $outbox->save();
        }
    }

    /**
     * Reintenta enviar a MongoDB los registros pendientes de la tabla 'audit_outboxes' de forma idempotente.
     */
    public static function retryPending(int $limit = 100): array
    {
        $pendingItems = AuditOutbox::where('status', 'pending')
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        $synced = 0;
        $failed = 0;

        foreach ($pendingItems as $item) {
            try {
                if (!extension_loaded('mongodb') && !class_exists(\MongoDB\Client::class)) {
                    throw new \RuntimeException('Driver ext-mongodb o cliente MongoDB no disponible.');
                }

                $existing = AuditLog::where('idempotency_key', $item->idempotency_key)->first();
                if (!$existing) {
                    AuditLog::create($item->payload);
                }

                $item->update([
                    'status'     => 'synced',
                    'synced_at'  => now(),
                    'last_error' => null,
                ]);
                $synced++;
            } catch (Throwable $e) {
                $item->update([
                    'attempts'   => $item->attempts + 1,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                ]);
                $failed++;
            }
        }

        return [
            'processed' => $pendingItems->count(),
            'synced'    => $synced,
            'failed'    => $failed,
        ];
    }
}

