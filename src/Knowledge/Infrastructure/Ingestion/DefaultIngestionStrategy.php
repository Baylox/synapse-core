<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Ingestion;

use App\Knowledge\Domain\Ingestion\DocumentReader;
use App\Knowledge\Domain\Ingestion\Embedder;
use App\Knowledge\Domain\Ingestion\IngestionStrategy;
use App\Knowledge\Domain\Ingestion\TextSplitter;
use App\Knowledge\Domain\Model\DocumentChunk;
use App\Knowledge\Domain\Model\KnowledgeDocument;
use App\Knowledge\Domain\Model\SourceType;

/**
 * Generic read -> split -> embed strategy. Acts as the fallback for any source
 * type; specialised strategies (contracts, invoices...) can override by
 * returning true from supports() for their type.
 */
final readonly class DefaultIngestionStrategy implements IngestionStrategy
{
    public function __construct(
        private DocumentReader $reader,
        private TextSplitter $splitter,
        private Embedder $embedder,
    ) {
    }

    public function supports(SourceType $sourceType): bool
    {
        return true;
    }

    public function ingest(KnowledgeDocument $document, string $absolutePath): array
    {
        $documentId = $document->id() ?? throw new \RuntimeException('Document must be persisted before ingestion.');

        $texts = $this->reader->read($absolutePath);
        $chunkTexts = $this->splitter->split($texts);
        $embeddings = $this->embedder->embedBatch($chunkTexts);

        $chunks = [];
        foreach ($chunkTexts as $index => $text) {
            $chunks[] = new DocumentChunk($documentId, $text, $index, $embeddings[$index]);
        }

        return $chunks;
    }
}
