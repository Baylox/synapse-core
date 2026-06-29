<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Model;

/**
 * The result of reasoning over a Question: the text, the citations that
 * grounded it, and a coarse trace of the steps taken (useful for agentic
 * strategies and debugging).
 */
final readonly class Answer
{
    /**
     * @param list<Citation> $citations
     * @param list<string>   $trace
     */
    public function __construct(
        public string $text,
        public array $citations = [],
        public array $trace = [],
    ) {
    }

    public function isGrounded(): bool
    {
        return $this->citations !== [];
    }
}
