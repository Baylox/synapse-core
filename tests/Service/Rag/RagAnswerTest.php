<?php

declare(strict_types=1);

namespace App\Tests\Service\Rag;

use App\Entity\DocumentChunk;
use App\Entity\KnowledgeDocument;
use App\Service\Rag\RagAnswer;
use PHPUnit\Framework\TestCase;

final class RagAnswerTest extends TestCase
{
    public function testUngroundedAnswerHasNoSources(): void
    {
        $answer = new RagAnswer('I don\'t know.', []);

        self::assertFalse($answer->isGrounded());
        self::assertSame([], $answer->sourceChunkIds());
    }

    public function testGroundedAnswerExposesSources(): void
    {
        $document = new KnowledgeDocument('Handbook', 'application/pdf', 'handbook.pdf');
        $chunk = new DocumentChunk($document, 'Some relevant text.', 0);

        $answer = new RagAnswer('Grounded reply.', [$chunk]);

        self::assertTrue($answer->isGrounded());
        // The chunk has no persisted id in a unit test, so the filtered list is empty
        // but the source objects are still available for the caller.
        self::assertCount(1, $answer->sources);
        self::assertSame('Handbook', $answer->sources[0]->getDocument()->getTitle());
    }
}
