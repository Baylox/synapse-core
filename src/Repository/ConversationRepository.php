<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function save(Conversation $conversation, bool $flush = true): void
    {
        $this->getEntityManager()->persist($conversation);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
