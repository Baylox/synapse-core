<?php

declare(strict_types=1);

namespace App\Reasoning\Infrastructure\Strategy;

use App\Reasoning\Domain\Model\Question;
use App\Reasoning\Domain\Strategy\ReasoningStrategies;
use App\Reasoning\Domain\Strategy\ReasoningStrategy;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Resolves the reasoning strategy for a question: the first tagged strategy
 * (by priority) whose supports() returns true. Specialised strategies are
 * tagged with higher priority than the RAG fallback.
 */
final readonly class ReasoningStrategyRegistry implements ReasoningStrategies
{
    /** @param iterable<ReasoningStrategy> $strategies */
    public function __construct(
        #[AutowireIterator('app.reasoning_strategy')]
        private iterable $strategies,
    ) {
    }

    public function forQuestion(Question $question): ReasoningStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($question)) {
                return $strategy;
            }
        }

        throw new \RuntimeException('No reasoning strategy supports this question.');
    }
}
