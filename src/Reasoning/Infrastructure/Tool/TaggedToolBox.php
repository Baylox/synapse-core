<?php

declare(strict_types=1);

namespace App\Reasoning\Infrastructure\Tool;

use App\Reasoning\Domain\Port\Tool;
use App\Reasoning\Domain\Port\ToolBox;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Discovers every service tagged `app.reasoning_tool`. Starts empty; add tools
 * by implementing Tool — they are auto-tagged and become available to the
 * agentic strategy without any wiring change.
 */
final readonly class TaggedToolBox implements ToolBox
{
    /** @param iterable<Tool> $tools */
    public function __construct(
        #[AutowireIterator('app.reasoning_tool')]
        private iterable $tools,
    ) {
    }

    public function all(): array
    {
        return iterator_to_array($this->tools, false);
    }

    public function get(string $name): ?Tool
    {
        foreach ($this->tools as $tool) {
            if ($tool->name() === $name) {
                return $tool;
            }
        }

        return null;
    }
}
