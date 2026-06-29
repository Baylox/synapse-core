<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

/**
 * Marker for something that happened in the domain and is worth reacting to.
 * Implementations are immutable value objects.
 */
interface DomainEvent
{
    public function occurredOn(): \DateTimeImmutable;
}
