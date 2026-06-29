<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Model;

/**
 * What the user asked, plus optional hints that let a ReasoningStrategy decide
 * whether it applies (strategy-per-input seam).
 */
final readonly class Question
{
    /** @param array<string, scalar> $hints */
    public function __construct(
        public string $text,
        public array $hints = [],
    ) {
    }

    public function hint(string $key): string|int|float|bool|null
    {
        return $this->hints[$key] ?? null;
    }
}
