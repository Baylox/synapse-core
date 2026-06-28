<?php

declare(strict_types=1);

namespace App\Dto\Response;

use App\Entity\KnowledgeDocument;

/**
 * Read model for a KnowledgeDocument exposed by the documents API.
 */
final readonly class DocumentView
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $status,
        public int $chunks,
        public string $createdAt,
    ) {
    }

    public static function fromEntity(KnowledgeDocument $document): self
    {
        return new self(
            id: $document->getId(),
            title: $document->getTitle(),
            status: $document->getStatus()->value,
            chunks: $document->getChunkCount(),
            createdAt: $document->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
