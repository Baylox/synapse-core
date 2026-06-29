<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Repository;

use App\Knowledge\Domain\Model\DocumentChunk;
use App\Shared\Domain\ValueObject\Embedding;

/**
 * Port: persistence + vector search for DocumentChunk.
 */
interface DocumentChunks
{
    /** @param iterable<DocumentChunk> $chunks */
    public function add(iterable $chunks): void;

    public function removeForDocument(int $documentId): void;

    /** @return list<DocumentChunk> nearest neighbours to the given embedding */
    public function searchNearest(Embedding $embedding, int $limit): array;
}
