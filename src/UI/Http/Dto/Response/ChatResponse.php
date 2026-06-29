<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Response;

use App\Reasoning\Domain\Model\Answer;

final readonly class ChatResponse
{
    /**
     * @param list<CitationView> $sources
     * @param list<string>       $trace
     */
    public function __construct(
        public string $answer,
        public bool $grounded,
        public array $sources,
        public array $trace,
    ) {
    }

    public static function fromAnswer(Answer $answer): self
    {
        return new self(
            answer: $answer->text,
            grounded: $answer->isGrounded(),
            sources: array_map(CitationView::fromCitation(...), $answer->citations),
            trace: $answer->trace,
        );
    }
}
