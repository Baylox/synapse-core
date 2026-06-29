<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Port;

/**
 * Port: the set of tools available to agentic reasoning.
 */
interface ToolBox
{
    /** @return list<Tool> */
    public function all(): array;

    public function get(string $name): ?Tool;
}
