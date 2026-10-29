<?php

namespace App\Console\Commands;

use App\Services\AuditOutboxService;
use Illuminate\Console\Command;

class RetryAuditOutboxCommand extends Command
{
    protected $signature = 'outbox:retry {--limit=100 : Número máximo de registros pendientes a procesar}';

    protected $description = 'Reintenta de forma idempotente la sincronización de bitácoras pendientes hacia MongoDB (Patrón Outbox)';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $result = AuditOutboxService::retryPending($limit);

        $this->info("Outbox procesado: {$result['processed']} revisados, {$result['synced']} sincronizados, {$result['failed']} fallidos.");

        return self::SUCCESS;
    }
}

