<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Author of a {@see \App\Entity\ChatMessage}, mirroring the OpenAI chat roles.
 */
enum MessageRole: string
{
    case System = 'system';
    case User = 'user';
    case Assistant = 'assistant';
}
