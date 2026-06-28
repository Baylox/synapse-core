<?php

declare(strict_types=1);

namespace App\Dto\Response;

use App\Service\Rag\RagAnswer;

/**
 * Typed response for POST /api/chat, built from the service-layer RagAnswer.
 */
final readonly class ChatResponse
{
    /** @param list<SourceView> $sources */
    public function __construct(
        public string $answer,
        public bool $grounded,
        public array $sources,
    ) {
    }

    public static function fromRagAnswer(RagAnswer $answer): self
    {
        return new self(
            answer: $answer->answer,
            grounded: $answer->isGrounded(),
            sources: array_map(SourceView::fromChunk(...), $answer->sources),
        );
    }
}
