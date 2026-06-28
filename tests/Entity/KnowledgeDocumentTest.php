<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\KnowledgeDocument;
use App\Enum\DocumentStatus;
use PHPUnit\Framework\TestCase;

final class KnowledgeDocumentTest extends TestCase
{
    public function testLifecycleTransitions(): void
    {
        $document = new KnowledgeDocument('Report.pdf', 'application/pdf', 'report.pdf');
        self::assertSame(DocumentStatus::Pending, $document->getStatus());
        self::assertNull($document->getIngestedAt());

        $document->markProcessing();
        self::assertSame(DocumentStatus::Processing, $document->getStatus());

        $document->markIngested(12);
        self::assertSame(DocumentStatus::Ingested, $document->getStatus());
        self::assertSame(12, $document->getChunkCount());
        self::assertNotNull($document->getIngestedAt());
        self::assertTrue($document->getStatus()->isTerminal());
    }

    public function testFailureRecordsReason(): void
    {
        $document = new KnowledgeDocument('Report.pdf', 'application/pdf', 'report.pdf');
        $document->markFailed('Unsupported file');

        self::assertSame(DocumentStatus::Failed, $document->getStatus());
        self::assertSame('Unsupported file', $document->getErrorMessage());
    }
}
