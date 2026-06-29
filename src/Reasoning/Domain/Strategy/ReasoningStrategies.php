<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Strategy;

use App\Reasoning\Domain\Model\Question;

/**
 * Resolves which ReasoningStrategy handles a given question.
 */
interface ReasoningStrategies
{
    public function forQuestion(Question $question): ReasoningStrategy;
}
