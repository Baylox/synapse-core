<?php

declare(strict_types=1);

namespace App\Service\Rag;

use App\Entity\KnowledgeDocument;
use App\Message\IngestDocument;
use App\Repository\KnowledgeDocumentRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Accepts an uploaded file, stores it under APP_SHARE_DIR, records a
 * KnowledgeDocument in `pending` state, and dispatches async ingestion.
 *
 * The HTTP request returns immediately; embedding happens in a worker.
 */
final readonly class DocumentUploader
{
    public function __construct(
        private KnowledgeDocumentRepository $documents,
        private MessageBusInterface $bus,
        private SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/%env(APP_SHARE_DIR)%')]
        private string $shareDir,
    ) {
    }

    public function upload(UploadedFile $file): KnowledgeDocument
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = $this->slugger->slug($originalName)->lower();
        $storedName = sprintf('%s-%s.%s', $safeName, uniqid(), $file->guessExtension() ?? 'bin');

        $file->move($this->shareDir, $storedName);

        $document = new KnowledgeDocument(
            title: $file->getClientOriginalName(),
            sourceType: $file->getClientMimeType(),
            sourcePath: $storedName, // relative to APP_SHARE_DIR
        );
        $this->documents->save($document);

        $this->bus->dispatch(new IngestDocument($document->getId()));

        return $document;
    }
}
