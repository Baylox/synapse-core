<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Ingestion;

use App\Knowledge\Domain\Model\SourceType;

/**
 * Resolves the IngestionStrategy to use for a source type. Implemented by an
 * infrastructure registry fed with all tagged strategies.
 */
interface IngestionStrategies
{
    public function forSource(SourceType $sourceType): IngestionStrategy;
}
