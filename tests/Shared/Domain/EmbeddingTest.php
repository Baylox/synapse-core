<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain;

use App\Shared\Domain\ValueObject\Embedding;
use PHPUnit\Framework\TestCase;

final class EmbeddingTest extends TestCase
{
    public function testDimensionReflectsValues(): void
    {
        $embedding = new Embedding([0.1, 0.2, 0.3]);

        self::assertSame(3, $embedding->dimension());
        self::assertSame([0.1, 0.2, 0.3], $embedding->toArray());
    }

    public function testEmptyEmbeddingIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Embedding([]);
    }
}
