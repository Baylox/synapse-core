<?php

declare(strict_types=1);

namespace App\UI\Http\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class ChatRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'A non-empty "question" is required.')]
        #[Assert\Length(max: 2000)]
        public string $question = '',

        /** Optional reasoning hint, e.g. "agentic" to force the agentic strategy. */
        #[Assert\Choice(choices: ['rag', 'agentic'])]
        public ?string $mode = null,
    ) {
    }

    /** @return array<string, scalar> */
    public function hints(): array
    {
        return $this->mode !== null ? ['mode' => $this->mode] : [];
    }
}
