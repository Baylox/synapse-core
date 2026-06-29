<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Ingestion;

/**
 * Port: split raw text into embeddable chunks.
 */
interface TextSplitter
{
    /**
     * @param list<string> $texts
     * @return list<string> chunked text
     */
    public function split(array $texts): array;
}
