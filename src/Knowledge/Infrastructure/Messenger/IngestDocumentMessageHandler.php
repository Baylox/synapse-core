<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Messenger;

use App\Knowledge\Application\IngestDocumentHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Driving adapter: bridges the Messenger transport to the IngestDocument use case.
 */
#[AsMessageHandler]
final readonly class IngestDocumentMessageHandler
{
    public function __construct(private IngestDocumentHandler $handler)
    {
    }

    public function __invoke(IngestDocumentMessage $message): void
    {
        ($this->handler)($message->documentId);
    }
}
