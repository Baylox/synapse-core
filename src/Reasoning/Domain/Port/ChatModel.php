<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Port;

/**
 * Port: a chat-completion LLM. Keeps the domain independent of OpenAI/LLPhant.
 */
interface ChatModel
{
    public function complete(string $systemPrompt, string $userPrompt): string;
}
