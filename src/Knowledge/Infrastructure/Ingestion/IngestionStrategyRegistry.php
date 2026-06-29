<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Ingestion;

use App\Knowledge\Domain\Ingestion\IngestionStrategies;
use App\Knowledge\Domain\Ingestion\IngestionStrategy;
use App\Knowledge\Domain\Model\SourceType;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Picks the first tagged strategy that supports a source type. Specialised
 * strategies are registered with a higher priority than DefaultIngestionStrategy
 * so they win for the types they claim.
 */
final readonly class IngestionStrategyRegistry implements IngestionStrategies
{
    /** @param iterable<IngestionStrategy> $strategies */
    public function __construct(
        #[AutowireIterator('app.ingestion_strategy')]
        private iterable $strategies,
    ) {
    }

    public function forSource(SourceType $sourceType): IngestionStrategy
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($sourceType)) {
                return $strategy;
            }
        }

        throw new \RuntimeException(sprintf('No ingestion strategy supports source type "%s".', $sourceType->value));
    }
}
