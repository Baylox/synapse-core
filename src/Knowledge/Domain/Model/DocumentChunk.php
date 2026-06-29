<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Model;

use App\Shared\Domain\ValueObject\Embedding;

/**
 * An embedded, retrievable slice of a KnowledgeDocument. References its owning
 * document by id (see KnowledgeDocument for why chunks live outside the
 * aggregate's in-memory boundary).
 */
class DocumentChunk
{
    private ?int $id = null;
    private string $hash;

    public function __construct(
        private int $documentId,
        private string $content,
        private int $chunkNumber,
        private Embedding $embedding,
    ) {
        $this->hash = hash('sha256', $content);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function documentId(): int
    {
        return $this->documentId;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function chunkNumber(): int
    {
        return $this->chunkNumber;
    }

    public function embedding(): Embedding
    {
        return $this->embedding;
    }

    public function hash(): string
    {
        return $this->hash;
    }
}
