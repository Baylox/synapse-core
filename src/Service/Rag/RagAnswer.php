<?php

declare(strict_types=1);

namespace App\Service\Rag;

use App\Entity\DocumentChunk;

/**
 * Result of a RAG query: the generated answer plus the chunks that grounded
 * it, so the UI can show citations and the caller can persist provenance.
 */
final readonly class RagAnswer
{
    /** @param list<DocumentChunk> $sources */
    public function __construct(
        public string $answer,
        public array $sources,
    ) {
    }

    /** @return list<int> */
    public function sourceChunkIds(): array
    {
        return array_values(array_filter(array_map(
            static fn (DocumentChunk $chunk): ?int => $chunk->getId(),
            $this->sources,
        )));
    }

    public function isGrounded(): bool
    {
        return $this->sources !== [];
    }
}
