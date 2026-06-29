<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Ingestion;

use App\Knowledge\Domain\Model\DocumentChunk;
use App\Knowledge\Domain\Model\KnowledgeDocument;
use App\Knowledge\Domain\Model\SourceType;

/**
 * Strategy seam (multi-source): how a given kind of document is turned into
 * embedded chunks. New source types (contracts, invoices, e-mails...) add a
 * new strategy without touching the application layer.
 */
interface IngestionStrategy
{
    public function supports(SourceType $sourceType): bool;

    /**
     * @return list<DocumentChunk>
     */
    public function ingest(KnowledgeDocument $document, string $absolutePath): array;
}
