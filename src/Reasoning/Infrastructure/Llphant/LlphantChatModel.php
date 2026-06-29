<?php

declare(strict_types=1);

namespace App\Reasoning\Infrastructure\Llphant;

use App\Reasoning\Domain\Port\ChatModel;
use LLPhant\Chat\OpenAIChat;

final readonly class LlphantChatModel implements ChatModel
{
    public function __construct(private OpenAIChat $chat)
    {
    }

    public function complete(string $systemPrompt, string $userPrompt): string
    {
        $this->chat->setSystemMessage($systemPrompt);

        return $this->chat->generateText($userPrompt);
    }
}
