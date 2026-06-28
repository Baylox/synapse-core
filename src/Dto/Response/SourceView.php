<?php

declare(strict_types=1);

namespace App\Dto\Response;

use App\Entity\DocumentChunk;

/**
 * A single citation surfaced alongside an answer.
 */
final readonly class SourceView
{
    public function __construct(
        public ?int $id,
        public string $document,
        public string $excerpt,
    ) {
    }

    public static function fromChunk(DocumentChunk $chunk): self
    {
        return new self(
            id: $chunk->getId(),
            document: $chunk->getDocument()->getTitle(),
            excerpt: mb_substr($chunk->getContent(), 0, 200),
        );
    }
}
