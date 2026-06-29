<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Shared\Domain\ValueObject\Embedding;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use LLPhant\Embeddings\VectorStores\Doctrine\VectorUtils;

/**
 * Doctrine custom type mapping the Embedding value object onto a pgvector
 * `vector(N)` column. Keeps the domain free of any persistence concern: the
 * model only ever sees an Embedding, never a raw array or DB string.
 */
final class EmbeddingType extends Type
{
    public const NAME = 'embedding';

    /**
     * Dimension used when none is carried by the column (e.g. during schema
     * introspection, where pgvector's `vector(N)` modifier is not reported).
     * Matches the application's embedding model (text-embedding-3-small).
     */
    public const DEFAULT_DIMENSIONS = 1536;

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $length = $column['length'] ?? self::DEFAULT_DIMENSIONS;
        if (!\is_int($length) || $length < 1) {
            throw new \InvalidArgumentException('An embedding column requires a positive integer length.');
        }

        return sprintf('vector(%d)', $length);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Embedding
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = \is_resource($value) ? stream_get_contents($value) : $value;
        if (!\is_string($value)) {
            throw new \InvalidArgumentException('Cannot convert database value to an Embedding.');
        }

        $floats = array_map(
            static fn (string $n): float => (float) $n,
            explode(',', trim($value, "[] \t\n\r")),
        );

        return $floats === [] ? null : new Embedding(array_values($floats));
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        $array = $value instanceof Embedding ? $value->toArray() : $value;
        if (!\is_array($array)) {
            throw new \InvalidArgumentException('An embedding must be an Embedding value object or a float array.');
        }

        return VectorUtils::getVectorAsString($array);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
