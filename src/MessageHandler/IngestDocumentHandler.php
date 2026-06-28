<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\IngestDocument;
use App\Repository\KnowledgeDocumentRepository;
use App\Service\Rag\DocumentIngestor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class IngestDocumentHandler
{
    public function __construct(
        private KnowledgeDocumentRepository $documents,
        private DocumentIngestor $ingestor,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(IngestDocument $message): void
    {
        $document = $this->documents->find($message->documentId);

        if ($document === null) {
            $this->logger->warning('IngestDocument: document not found', ['id' => $message->documentId]);

            return;
        }

        $this->ingestor->ingest($document);
    }
}
