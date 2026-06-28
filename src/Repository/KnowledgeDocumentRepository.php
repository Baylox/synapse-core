<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\KnowledgeDocument;
use App\Enum\DocumentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<KnowledgeDocument>
 */
class KnowledgeDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, KnowledgeDocument::class);
    }

    public function save(KnowledgeDocument $document, bool $flush = true): void
    {
        $this->getEntityManager()->persist($document);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return list<KnowledgeDocument> */
    public function findByStatus(DocumentStatus $status): array
    {
        return $this->findBy(['status' => $status], ['createdAt' => 'DESC']);
    }

    /** @return list<KnowledgeDocument> */
    public function findAllNewestFirst(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }
}
