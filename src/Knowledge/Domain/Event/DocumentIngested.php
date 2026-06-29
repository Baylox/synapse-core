<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;

final readonly class DocumentIngested implements DomainEvent
{
    public function __construct(
        public int $documentId,
        public int $chunkCount,
        private \DateTimeImmutable $occurredOn,
    ) {
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
