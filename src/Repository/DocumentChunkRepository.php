<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DocumentChunk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LLPhant\Embeddings\VectorStores\Doctrine\VectorUtils;

/**
 * @extends ServiceEntityRepository<DocumentChunk>
 */
class DocumentChunkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentChunk::class);
    }

    /**
     * Approximate nearest-neighbour search over the pgvector column.
     *
     * Relies on the `L2_DISTANCE` DQL function (registered in doctrine.yaml,
     * provided by LLPhant) which maps to pgvector's `<->` operator. For
     * OpenAI's normalised embeddings, L2 ordering matches cosine ordering.
     *
     * @param list<float> $embedding query embedding, same dimension as the column
     * @return list<DocumentChunk>
     */
    public function findNearest(array $embedding, int $k = 4): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.embedding IS NOT NULL')
            ->orderBy('L2_DISTANCE(c.embedding, :vector)', 'ASC')
            ->setParameter('vector', VectorUtils::getVectorAsString($embedding))
            ->setMaxResults($k)
            ->getQuery()
            ->getResult();
    }
}
