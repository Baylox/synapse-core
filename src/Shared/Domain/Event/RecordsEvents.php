<?php

declare(strict_types=1);

namespace App\Shared\Domain\Event;

/**
 * Mixed into aggregate roots so they can record domain events that the
 * application layer pulls and dispatches after persistence.
 */
trait RecordsEvents
{
    /** @var list<DomainEvent> */
    private array $recordedEvents = [];

    protected function recordEvent(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
    }

    /** @return list<DomainEvent> */
    public function pullDomainEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }
}
