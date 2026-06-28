<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DocumentChunkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LLPhant\Embeddings\VectorStores\Doctrine\VectorType;

/**
 * A single embedded slice of a {@see KnowledgeDocument}.
 *
 * The high-dimensional `embedding` is stored in a native pgvector `vector(N)`
 * column. We reuse LLPhant's {@see VectorType} Doctrine type (registered in
 * config/packages/doctrine.yaml) so Doctrine can hydrate the float array
 * transparently, and the `L2_DISTANCE` DQL function for nearest-neighbour
 * search (see {@see DocumentChunkRepository::findNearest()}).
 *
 * IMPORTANT: the vector length below (1536) matches OpenAI's
 * `text-embedding-3-small`. Switching embedding model means changing this
 * length AND adding a migration that re-creates the column + re-embeds.
 */
#[ORM\Entity(repositoryClass: DocumentChunkRepository::class)]
#[ORM\Table(name: 'document_chunk')]
#[ORM\Index(name: 'idx_chunk_document', columns: ['document_id'])]
class DocumentChunk
{
    public const EMBEDDING_DIMENSIONS = 1536;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: KnowledgeDocument::class, inversedBy: 'chunks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private KnowledgeDocument $document;

    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    /** @var list<float>|null */
    #[ORM\Column(type: VectorType::VECTOR, length: self::EMBEDDING_DIMENSIONS, nullable: true)]
    private ?array $embedding = null;

    /** 0-based position of this chunk inside the source document. */
    #[ORM\Column]
    private int $chunkNumber;

    /** SHA-256 of the content, used to skip re-embedding unchanged chunks. */
    #[ORM\Column(length: 64)]
    private string $hash;

    public function __construct(KnowledgeDocument $document, string $content, int $chunkNumber)
    {
        $this->document = $document;
        $this->content = $content;
        $this->chunkNumber = $chunkNumber;
        $this->hash = hash('sha256', $content);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): KnowledgeDocument
    {
        return $this->document;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /** @return list<float>|null */
    public function getEmbedding(): ?array
    {
        return $this->embedding;
    }

    /** @param list<float> $embedding */
    public function setEmbedding(array $embedding): void
    {
        $this->embedding = $embedding;
    }

    public function getChunkNumber(): int
    {
        return $this->chunkNumber;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
