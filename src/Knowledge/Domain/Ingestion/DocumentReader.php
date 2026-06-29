<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Ingestion;

/**
 * Port: extract raw text from a stored file (PDF, Word, plain text...).
 */
interface DocumentReader
{
    /**
     * @return list<string> one or more raw text blocks
     */
    public function read(string $absolutePath): array;
}
