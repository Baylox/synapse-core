<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Llphant;

use App\Knowledge\Domain\Ingestion\DocumentReader;
use LLPhant\Embeddings\DataReader\FileDataReader;

final class LlphantDocumentReader implements DocumentReader
{
    public function read(string $absolutePath): array
    {
        $documents = (new FileDataReader($absolutePath))->getDocuments();

        return array_map(static fn ($document): string => $document->content, $documents);
    }
}
