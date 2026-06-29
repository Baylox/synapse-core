<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Response;

use App\Reasoning\Domain\Model\Citation;

final readonly class CitationView
{
    public function __construct(
        public ?int $id,
        public string $document,
        public string $excerpt,
    ) {
    }

    public static function fromCitation(Citation $citation): self
    {
        return new self($citation->chunkId, $citation->documentTitle, $citation->excerpt);
    }
}
