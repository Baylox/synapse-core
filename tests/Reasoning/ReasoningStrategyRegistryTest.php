<?php

declare(strict_types=1);

namespace App\Tests\Reasoning;

use App\Reasoning\Domain\Model\Answer;
use App\Reasoning\Domain\Model\Question;
use App\Reasoning\Domain\Strategy\ReasoningStrategy;
use App\Reasoning\Infrastructure\Strategy\ReasoningStrategyRegistry;
use PHPUnit\Framework\TestCase;

final class ReasoningStrategyRegistryTest extends TestCase
{
    public function testPicksTheFirstSupportingStrategy(): void
    {
        $agentic = $this->strategy(fn (Question $q) => $q->hint('mode') === 'agentic', 'agentic');
        $rag = $this->strategy(fn (Question $q) => true, 'rag');

        $registry = new ReasoningStrategyRegistry([$agentic, $rag]);

        self::assertSame('agentic', $registry->forQuestion(new Question('x', ['mode' => 'agentic']))->reason(new Question('x'))->text);
        self::assertSame('rag', $registry->forQuestion(new Question('x'))->reason(new Question('x'))->text);
    }

    public function testThrowsWhenNothingSupports(): void
    {
        $registry = new ReasoningStrategyRegistry([$this->strategy(fn () => false, 'never')]);

        $this->expectException(\RuntimeException::class);
        $registry->forQuestion(new Question('x'));
    }

    private function strategy(callable $supports, string $label): ReasoningStrategy
    {
        return new class($supports, $label) implements ReasoningStrategy {
            public function __construct(private $supports, private string $label)
            {
            }

            public function supports(Question $question): bool
            {
                return ($this->supports)($question);
            }

            public function reason(Question $question): Answer
            {
                return new Answer($this->label);
            }
        };
    }
}
