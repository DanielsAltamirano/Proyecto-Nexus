<?php

namespace App\Models;

use LogicException;

if (class_exists(\MongoDB\Laravel\Eloquent\Model::class)) {
    abstract class BaseMongoAuditModel extends \MongoDB\Laravel\Eloquent\Model {}
} else {
    abstract class BaseMongoAuditModel extends \Illuminate\Database\Eloquent\Model {}
}

/**
 * Modelo AuditLog (MongoDB)
 * Bitácora inmutable de auditoría y almacenamiento de recetas médicas (medicamentos controlados).
 */
class AuditLog extends BaseMongoAuditModel
{
    protected $connection = 'mongodb';

    protected $collection = 'audit_logs';

    protected $table = 'audit_logs';

    protected $fillable = [
        'idempotency_key',
        'event_type',
        'sale_id',
        'receipt_id',
        'product_id',
        'product_name',
        'sku',
        'is_controlled',
        'cedula_medica',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'status_history',
        'user_id',
        'branch_id',
        'client_id',
        'prescription_data',
        'metadata',
        'recorded_at',
    ];

    protected $casts = [
        'is_controlled'     => 'boolean',
        'quantity'          => 'integer',
        'unit_price'        => 'float',
        'total_price'       => 'float',
        'status_history'    => 'array',
        'prescription_data' => 'array',
        'metadata'          => 'array',
        'recorded_at'       => 'datetime',
    ];

    /**
     * Garantiza que la bitácora de auditoría y recetas médicas sea estrictamente INMUTABLE.
     * No se permiten actualizaciones ni eliminaciones una vez insertado el documento.
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('AuditLog es inmutable: no se permite modificar registros de auditoría o recetas médicas.');
        });

        static::deleting(function () {
            throw new LogicException('AuditLog es inmutable: no se permite eliminar registros de auditoría o recetas médicas.');
        });
    }
}

