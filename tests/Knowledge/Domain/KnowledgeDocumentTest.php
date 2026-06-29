<?php

declare(strict_types=1);

namespace App\Tests\Knowledge\Domain;

use App\Knowledge\Domain\Event\DocumentIngested;
use App\Knowledge\Domain\Event\DocumentIngestionFailed;
use App\Knowledge\Domain\Model\DocumentStatus;
use App\Knowledge\Domain\Model\KnowledgeDocument;
use App\Knowledge\Domain\Model\SourceType;
use PHPUnit\Framework\TestCase;

final class KnowledgeDocumentTest extends TestCase
{
    public function testIngestionRecordsADomainEvent(): void
    {
        $document = new KnowledgeDocument('Report.pdf', SourceType::Pdf, 'report.pdf');
        self::assertSame(DocumentStatus::Pending, $document->status());

        $document->startProcessing();
        self::assertSame(DocumentStatus::Processing, $document->status());

        $document->markIngested(12);
        self::assertSame(DocumentStatus::Ingested, $document->status());
        self::assertSame(12, $document->chunkCount());
        self::assertTrue($document->status()->isTerminal());

        $events = $document->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(DocumentIngested::class, $events[0]);
        self::assertSame([], $document->pullDomainEvents(), 'events are pulled once');
    }

    public function testFailureRecordsEventAndReason(): void
    {
        $document = new KnowledgeDocument('Report.pdf', SourceType::Pdf, 'report.pdf');
        $document->markFailed('Unsupported file');

        self::assertSame(DocumentStatus::Failed, $document->status());
        self::assertSame('Unsupported file', $document->errorMessage());

        $events = $document->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(DocumentIngestionFailed::class, $events[0]);
    }

    public function testSourceTypeIsDerivedFromMimeType(): void
    {
        self::assertSame(SourceType::Pdf, SourceType::fromMimeType('application/pdf'));
        self::assertSame(SourceType::Text, SourceType::fromMimeType('text/plain'));
        self::assertSame(SourceType::Unknown, SourceType::fromMimeType('image/png'));
    }
}
