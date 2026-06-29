<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Model;

enum DocumentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Ingested = 'ingested';
    case Failed = 'failed';

    public function isTerminal(): bool
    {
        return $this === self::Ingested || $this === self::Failed;
    }
}
