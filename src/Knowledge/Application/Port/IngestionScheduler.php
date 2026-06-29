<?php

declare(strict_types=1);

namespace App\Knowledge\Application\Port;

/**
 * Port: schedule the (async) ingestion of a document. The adapter decides the
 * transport (Messenger today).
 */
interface IngestionScheduler
{
    public function schedule(int $documentId): void;
}
