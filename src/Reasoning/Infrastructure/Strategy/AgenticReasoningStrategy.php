<?php

declare(strict_types=1);

namespace App\Reasoning\Infrastructure\Strategy;

use App\Reasoning\Domain\Model\Answer;
use App\Reasoning\Domain\Model\Question;
use App\Reasoning\Domain\Port\ChatModel;
use App\Reasoning\Domain\Port\ToolBox;
use App\Reasoning\Domain\Strategy\ReasoningStrategy;

/**
 * Agentic strategy seam: a tool-using reasoner. Selected when the question is
 * hinted with mode=agentic. The full ReAct / function-calling loop is a TODO;
 * the structure (tool discovery, bounded steps, trace) is in place so it can be
 * filled in without touching the application or domain layers.
 */
final readonly class AgenticReasoningStrategy implements ReasoningStrategy
{
    private const MAX_STEPS = 5;

    public function __construct(
        private ChatModel $chat,
        private ToolBox $tools,
    ) {
    }

    public function supports(Question $question): bool
    {
        return $question->hint('mode') === 'agentic';
    }

    public function reason(Question $question): Answer
    {
        $trace = ['strategy: agentic', sprintf('tools available: %d', \count($this->tools->all()))];

        // TODO: implement the ReAct loop —
        //   1. ask the model to pick a tool + arguments (function calling),
        //   2. run the tool via $this->tools->get($name)->run($args),
        //   3. feed the observation back, repeat up to self::MAX_STEPS,
        //   4. stop when the model returns a final answer.
        $toolCatalog = implode(', ', array_map(
            static fn ($tool): string => $tool->name(),
            $this->tools->all(),
        )) ?: 'none';

        $system = sprintf(
            'You are Synapse in agentic mode. Available tools: %s. (Tool execution loop not wired yet.)',
            $toolCatalog,
        );
        $text = $this->chat->complete($system, $question->text);

        $trace[] = sprintf('max steps: %d (loop pending)', self::MAX_STEPS);

        return new Answer($text, [], $trace);
    }
}
