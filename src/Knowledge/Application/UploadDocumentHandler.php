<?php

declare(strict_types=1);

namespace App\Knowledge\Application;

use App\Knowledge\Application\Command\UploadDocumentCommand;
use App\Knowledge\Application\Port\IngestionScheduler;
use App\Knowledge\Domain\Model\KnowledgeDocument;
use App\Knowledge\Domain\Model\SourceType;
use App\Knowledge\Domain\Repository\KnowledgeDocuments;
use App\Knowledge\Domain\Service\FileStorage;

/**
 * Use case: store an uploaded file, register it as a pending document, and
 * schedule async ingestion.
 */
final readonly class UploadDocumentHandler
{
    public function __construct(
        private KnowledgeDocuments $documents,
        private FileStorage $storage,
        private IngestionScheduler $scheduler,
    ) {
    }

    public function __invoke(UploadDocumentCommand $command): int
    {
        $storedPath = $this->storage->store($command->temporaryPath, $command->originalName);

        $document = new KnowledgeDocument(
            $command->originalName,
            SourceType::fromMimeType($command->mimeType),
            $storedPath,
        );
        $this->documents->save($document);

        $id = $document->id() ?? throw new \RuntimeException('Document was not assigned an id.');
        $this->scheduler->schedule($id);

        return $id;
    }
}
