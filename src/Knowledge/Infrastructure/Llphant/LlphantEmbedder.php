<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Llphant;

use App\Knowledge\Domain\Ingestion\Embedder;
use App\Shared\Domain\ValueObject\Embedding;
use LLPhant\Embeddings\Document as LlphantDocument;
use LLPhant\Embeddings\EmbeddingGenerator\EmbeddingGeneratorInterface;

final readonly class LlphantEmbedder implements Embedder
{
    public function __construct(private EmbeddingGeneratorInterface $generator)
    {
    }

    public function embed(string $text): Embedding
    {
        return new Embedding($this->generator->embedText($text));
    }

    public function embedBatch(array $texts): array
    {
        $documents = [];
        foreach ($texts as $text) {
            $document = new LlphantDocument();
            $document->content = $text;
            $documents[] = $document;
        }

        $embedded = $this->generator->embedDocuments($documents);

        return array_map(
            static fn (LlphantDocument $document): Embedding => new Embedding($document->embedding ?? []),
            $embedded,
        );
    }
}
