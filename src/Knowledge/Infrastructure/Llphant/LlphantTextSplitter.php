<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Llphant;

use App\Knowledge\Domain\Ingestion\TextSplitter;
use LLPhant\Embeddings\Document as LlphantDocument;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class LlphantTextSplitter implements TextSplitter
{
    public function __construct(
        #[Autowire('%env(int:RAG_CHUNK_SIZE)%')]
        private int $chunkSize = 1000,
    ) {
    }

    public function split(array $texts): array
    {
        $documents = [];
        foreach ($texts as $text) {
            $document = new LlphantDocument();
            $document->content = $text;
            $documents[] = $document;
        }

        $split = DocumentSplitter::splitDocuments($documents, $this->chunkSize);

        return array_values(array_map(
            static fn (LlphantDocument $document): string => $document->content,
            $split,
        ));
    }
}
