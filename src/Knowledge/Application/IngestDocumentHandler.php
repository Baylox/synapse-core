<?php

declare(strict_types=1);

namespace App\Knowledge\Application;

use App\Knowledge\Domain\Ingestion\IngestionStrategies;
use App\Knowledge\Domain\Repository\DocumentChunks;
use App\Knowledge\Domain\Repository\KnowledgeDocuments;
use App\Knowledge\Domain\Service\FileStorage;
use App\Shared\Domain\Event\DomainEventDispatcher;
use Psr\Log\LoggerInterface;

/**
 * Use case: embed and index a document. Picks the right IngestionStrategy for
 * the source type, replaces any previous chunks, and dispatches the aggregate's
 * domain events after persistence.
 */
final readonly class IngestDocumentHandler
{
    public function __construct(
        private KnowledgeDocuments $documents,
        private DocumentChunks $chunks,
        private IngestionStrategies $strategies,
        private FileStorage $storage,
        private DomainEventDispatcher $events,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(int $documentId): void
    {
        $document = $this->documents->ofId($documentId);
        if ($document === null) {
            $this->logger->warning('Ingestion skipped: document not found', ['id' => $documentId]);

            return;
        }

        $document->startProcessing();
        $this->documents->save($document);

        try {
            $strategy = $this->strategies->forSource($document->sourceType());
            $chunks = $strategy->ingest($document, $this->storage->absolutePath($document->sourcePath()));

            $this->chunks->removeForDocument($documentId);
            $this->chunks->add($chunks);

            $document->markIngested(\count($chunks));
            $this->documents->save($document);
        } catch (\Throwable $exception) {
            $document->markFailed($exception->getMessage());
            $this->documents->save($document);
            $this->events->dispatch($document->pullDomainEvents());

            throw $exception;
        }

        $this->events->dispatch($document->pullDomainEvents());
    }
}
