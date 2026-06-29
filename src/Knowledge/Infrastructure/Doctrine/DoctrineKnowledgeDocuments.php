<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Doctrine;

use App\Knowledge\Domain\Model\KnowledgeDocument;
use App\Knowledge\Domain\Repository\KnowledgeDocuments;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineKnowledgeDocuments implements KnowledgeDocuments
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(KnowledgeDocument $document): void
    {
        $this->em->persist($document);
        $this->em->flush();
    }

    public function ofId(int $id): ?KnowledgeDocument
    {
        return $this->em->find(KnowledgeDocument::class, $id);
    }

    public function allNewestFirst(): array
    {
        return $this->em->getRepository(KnowledgeDocument::class)
            ->findBy([], ['createdAt' => 'DESC']);
    }
}
