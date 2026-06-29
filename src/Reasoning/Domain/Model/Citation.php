<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Model;

final readonly class Citation
{
    public function __construct(
        public ?int $chunkId,
        public string $documentTitle,
        public string $excerpt,
    ) {
    }
}
