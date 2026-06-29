<?php

declare(strict_types=1);

namespace App\Reasoning\Infrastructure\Knowledge;

use App\Knowledge\Domain\Ingestion\Embedder;
use App\Knowledge\Domain\Repository\DocumentChunks;
use App\Knowledge\Domain\Repository\KnowledgeDocuments;
use App\Reasoning\Domain\Model\Question;
use App\Reasoning\Domain\Model\RetrievedPassage;
use App\Reasoning\Domain\Port\KnowledgeRetriever;

/**
 * Anti-corruption adapter: implements the Reasoning context's retrieval port by
 * talking to the Knowledge context's ports. This is the only seam where the two
 * contexts meet.
 */
final readonly class KnowledgeBaseRetriever implements KnowledgeRetriever
{
    public function __construct(
        private Embedder $embedder,
        private DocumentChunks $chunks,
        private KnowledgeDocuments $documents,
    ) {
    }

    public function retrieve(Question $question, int $limit): array
    {
        $embedding = $this->embedder->embed($question->text);

        $titles = [];
        $passages = [];
        foreach ($this->chunks->searchNearest($embedding, $limit) as $chunk) {
            $documentId = $chunk->documentId();
            $titles[$documentId] ??= $this->documents->ofId($documentId)?->title() ?? 'Unknown source';
            $passages[] = new RetrievedPassage($chunk->id(), $titles[$documentId], $chunk->content());
        }

        return $passages;
    }
}
