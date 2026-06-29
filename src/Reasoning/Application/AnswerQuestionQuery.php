<?php

declare(strict_types=1);

namespace App\Reasoning\Application;

/**
 * @param array<string, scalar> $hints
 */
final readonly class AnswerQuestionQuery
{
    /** @param array<string, scalar> $hints */
    public function __construct(
        public string $question,
        public array $hints = [],
    ) {
    }
}
