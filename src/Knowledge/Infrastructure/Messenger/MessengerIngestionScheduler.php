<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Messenger;

use App\Knowledge\Application\Port\IngestionScheduler;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerIngestionScheduler implements IngestionScheduler
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public function schedule(int $documentId): void
    {
        $this->bus->dispatch(new IngestDocumentMessage($documentId));
    }
}
