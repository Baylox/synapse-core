<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Model;

use App\Knowledge\Domain\Event\DocumentIngested;
use App\Knowledge\Domain\Event\DocumentIngestionFailed;
use App\Shared\Domain\Event\RecordsEvents;

/**
 * Aggregate root of the Knowledge context. Owns the ingestion lifecycle and
 * its invariants; emits domain events on terminal transitions.
 *
 * Chunks are NOT held as an in-memory collection (a document can produce
 * thousands): they are a separate entity persisted via DocumentChunks and
 * reference this document by id. The aggregate guards the *document* state.
 */
class KnowledgeDocument
{
    use RecordsEvents;

    private ?int $id = null;
    private DocumentStatus $status = DocumentStatus::Pending;
    private int $chunkCount = 0;
    private ?string $errorMessage = null;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $ingestedAt = null;

    public function __construct(
        private string $title,
        private SourceType $sourceType,
        private string $sourcePath,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function startProcessing(): void
    {
        if ($this->status->isTerminal()) {
            $this->status = DocumentStatus::Pending; // allow re-ingestion
        }
        $this->status = DocumentStatus::Processing;
        $this->errorMessage = null;
    }

    public function markIngested(int $chunkCount): void
    {
        $this->status = DocumentStatus::Ingested;
        $this->chunkCount = $chunkCount;
        $this->ingestedAt = new \DateTimeImmutable();
        $this->errorMessage = null;
        $this->recordEvent(new DocumentIngested($this->id ?? 0, $chunkCount, $this->ingestedAt));
    }

    public function markFailed(string $reason): void
    {
        $this->status = DocumentStatus::Failed;
        $this->errorMessage = $reason;
        $this->recordEvent(new DocumentIngestionFailed($this->id ?? 0, $reason, new \DateTimeImmutable()));
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function sourceType(): SourceType
    {
        return $this->sourceType;
    }

    public function sourcePath(): string
    {
        return $this->sourcePath;
    }

    public function status(): DocumentStatus
    {
        return $this->status;
    }

    public function chunkCount(): int
    {
        return $this->chunkCount;
    }

    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function ingestedAt(): ?\DateTimeImmutable
    {
        return $this->ingestedAt;
    }
}
