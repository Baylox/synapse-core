<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Response;

use App\Knowledge\Domain\Model\KnowledgeDocument;

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

    public static function fromDocument(KnowledgeDocument $document): self
    {
        return new self(
            id: $document->id(),
            title: $document->title(),
            status: $document->status()->value,
            chunks: $document->chunkCount(),
            createdAt: $document->createdAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
