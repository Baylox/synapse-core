<?php

declare(strict_types=1);

namespace App\Reasoning\Application;

use App\Reasoning\Domain\Model\Answer;
use App\Reasoning\Domain\Model\Question;
use App\Reasoning\Domain\Strategy\ReasoningStrategies;

/**
 * Use case: answer a question by delegating to the strategy that fits the input.
 */
final readonly class AnswerQuestionHandler
{
    public function __construct(private ReasoningStrategies $strategies)
    {
    }

    public function __invoke(AnswerQuestionQuery $query): Answer
    {
        $question = new Question($query->question, $query->hints);

        return $this->strategies->forQuestion($question)->reason($question);
    }
}
