<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

/**
 * Port: hand recorded domain events off to whatever the infrastructure uses
 * (Symfony EventDispatcher, Messenger bus, an outbox...).
 */
interface DomainEventDispatcher
{
    /** @param iterable<DomainEvent> $events */
    public function dispatch(iterable $events): void;
}
