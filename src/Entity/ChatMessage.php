<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\MessageRole;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * One turn in a {@see Conversation}. `sources` optionally records which
 * {@see DocumentChunk} ids grounded an assistant answer (for citations).
 */
#[ORM\Entity]
#[ORM\Table(name: 'chat_message')]
#[ORM\Index(name: 'idx_message_conversation', columns: ['conversation_id'])]
class ChatMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Conversation $conversation = null;

    #[ORM\Column(enumType: MessageRole::class)]
    private MessageRole $role;

    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    /** @var list<int> Chunk ids that grounded an assistant answer. */
    #[ORM\Column(type: Types::JSON)]
    private array $sources = [];

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    /** @param list<int> $sources */
    public function __construct(MessageRole $role, string $content, array $sources = [])
    {
        $this->role = $role;
        $this->content = $content;
        $this->sources = $sources;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): void
    {
        $this->conversation = $conversation;
    }

    public function getRole(): MessageRole
    {
        return $this->role;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /** @return list<int> */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
