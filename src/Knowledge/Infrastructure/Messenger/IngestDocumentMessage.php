<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Messenger;

/**
 * Async message carrying a document id to ingest. Routed to the "async"
 * transport (see config/packages/messenger.yaml).
 */
final readonly class IngestDocumentMessage
{
    public function __construct(public int $documentId)
    {
    }
}
