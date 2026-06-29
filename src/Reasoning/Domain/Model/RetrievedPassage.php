<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Model;

/**
 * A piece of knowledge pulled from the knowledge base to ground an answer.
 */
final readonly class RetrievedPassage
{
    public function __construct(
        public ?int $chunkId,
        public string $documentTitle,
        public string $content,
    ) {
    }

    public function toCitation(): Citation
    {
        return new Citation($this->chunkId, $this->documentTitle, mb_substr($this->content, 0, 200));
    }
}
