<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\DocumentStatus;
use App\Repository\KnowledgeDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * A source document the user added to the knowledge base (a PDF, a Word file,
 * raw text...). It is the *domain* view of an ingested file; the actual
 * vector-searchable pieces live in {@see DocumentChunk}.
 */
#[ORM\Entity(repositoryClass: KnowledgeDocumentRepository::class)]
#[ORM\Table(name: 'knowledge_document')]
#[ORM\Index(name: 'idx_document_status', columns: ['status'])]
class KnowledgeDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    /** MIME type / reader hint (e.g. "application/pdf"). */
    #[ORM\Column(length: 100)]
    private string $sourceType;

    /** Absolute or APP_SHARE_DIR-relative path to the stored original file. */
    #[ORM\Column(length: 1024)]
    private string $sourcePath;

    #[ORM\Column(enumType: DocumentStatus::class)]
    private DocumentStatus $status = DocumentStatus::Pending;

    #[ORM\Column(options: ['default' => 0])]
    private int $chunkCount = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $ingestedAt = null;

    /** @var Collection<int, DocumentChunk> */
    #[ORM\OneToMany(targetEntity: DocumentChunk::class, mappedBy: 'document', cascade: ['remove'], orphanRemoval: true)]
    private Collection $chunks;

    public function __construct(string $title, string $sourceType, string $sourcePath)
    {
        $this->title = $title;
        $this->sourceType = $sourceType;
        $this->sourcePath = $sourcePath;
        $this->createdAt = new \DateTimeImmutable();
        $this->chunks = new ArrayCollection();
    }

    public function markProcessing(): void
    {
        $this->status = DocumentStatus::Processing;
        $this->errorMessage = null;
    }

    public function markIngested(int $chunkCount): void
    {
        $this->status = DocumentStatus::Ingested;
        $this->chunkCount = $chunkCount;
        $this->ingestedAt = new \DateTimeImmutable();
        $this->errorMessage = null;
    }

    public function markFailed(string $reason): void
    {
        $this->status = DocumentStatus::Failed;
        $this->errorMessage = $reason;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSourceType(): string
    {
        return $this->sourceType;
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function getStatus(): DocumentStatus
    {
        return $this->status;
    }

    public function getChunkCount(): int
    {
        return $this->chunkCount;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getIngestedAt(): ?\DateTimeImmutable
    {
        return $this->ingestedAt;
    }

    /** @return Collection<int, DocumentChunk> */
    public function getChunks(): Collection
    {
        return $this->chunks;
    }
}
