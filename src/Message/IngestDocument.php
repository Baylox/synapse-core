<?php

declare(strict_types=1);

namespace App\Message;

/**
 * Async command: embed and index the given KnowledgeDocument.
 * Routed to the "async" transport (see config/packages/messenger.yaml).
 */
final readonly class IngestDocument
{
    public function __construct(
        public int $documentId,
    ) {
    }
}
