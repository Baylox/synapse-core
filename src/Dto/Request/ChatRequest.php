<?php

declare(strict_types=1);

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Input payload for POST /api/chat. Mapped & validated automatically via
 * #[MapRequestPayload] — a failed constraint yields a 422 before the
 * controller body even runs.
 */
final class ChatRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'A non-empty "question" is required.')]
        #[Assert\Length(max: 2000)]
        public string $question = '',
    ) {
    }
}
