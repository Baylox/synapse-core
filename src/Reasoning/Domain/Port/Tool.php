<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Port;

/**
 * Port: a capability the agent can invoke during reasoning (function calling).
 * Concrete tools (web search, SQL lookup, calculator...) implement this and are
 * tagged so the ToolBox discovers them.
 */
interface Tool
{
    public function name(): string;

    public function description(): string;

    /**
     * @param array<string, mixed> $arguments
     */
    public function run(array $arguments): string;
}
