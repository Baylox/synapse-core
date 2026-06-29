<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

/**
 * An embedding vector. Pure domain value object: a non-empty list of floats
 * with a known dimension. Persistence (pgvector) is handled by an
 * infrastructure Doctrine type; the domain only knows "a vector".
 */
final readonly class Embedding
{
    /** @param list<float> $values */
    public function __construct(public array $values)
    {
        if ($values === []) {
            throw new \InvalidArgumentException('An embedding cannot be empty.');
        }
    }

    public function dimension(): int
    {
        return \count($this->values);
    }

    /** @return list<float> */
    public function toArray(): array
    {
        return $this->values;
    }
}
