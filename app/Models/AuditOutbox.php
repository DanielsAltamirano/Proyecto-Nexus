<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tabla relacional temporal para el Patrón Outbox hacia MongoDB.
 * Si la escritura en MongoDB falla tras registrar una venta en MySQL,
 * el evento se persiste aquí para reintento idempotente posterior.
 */
class AuditOutbox extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'audit_outboxes';

    protected $fillable = [
        'idempotency_key',
        'sale_id',
        'receipt_id',
        'collection',
        'event_type',
        'payload',
        'status',
        'attempts',
        'last_error',
        'synced_at',
    ];

    protected $casts = [
        'payload'   => 'array',
        'attempts'  => 'integer',
        'synced_at' => 'datetime',
    ];
}

