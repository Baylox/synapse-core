<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Lifecycle of a {@see \App\Entity\KnowledgeDocument} as it moves through the
 * ingestion pipeline (upload -> split -> embed -> index).
 */
enum DocumentStatus: string
{
    /** Stored on disk, queued for ingestion, nothing embedded yet. */
    case Pending = 'pending';

    /** An ingestion worker is currently splitting/embedding the document. */
    case Processing = 'processing';

    /** Fully embedded and searchable through the RAG pipeline. */
    case Ingested = 'ingested';

    /** Ingestion failed; see KnowledgeDocument::$errorMessage. */
    case Failed = 'failed';

    public function isTerminal(): bool
    {
        return $this === self::Ingested || $this === self::Failed;
    }
}
