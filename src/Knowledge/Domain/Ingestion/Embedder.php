<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Ingestion;

use App\Shared\Domain\ValueObject\Embedding;

/**
 * Port: turn text into embedding vectors. Implemented by an LLM adapter.
 */
interface Embedder
{
    public function embed(string $text): Embedding;

    /**
     * @param list<string> $texts
     * @return list<Embedding> aligned with the input order
     */
    public function embedBatch(array $texts): array;
}
