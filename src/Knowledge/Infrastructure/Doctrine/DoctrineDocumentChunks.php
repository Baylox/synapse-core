<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Doctrine;

use App\Knowledge\Domain\Model\DocumentChunk;
use App\Knowledge\Domain\Repository\DocumentChunks;
use App\Shared\Domain\ValueObject\Embedding;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use LLPhant\Embeddings\VectorStores\Doctrine\VectorUtils;

final readonly class DoctrineDocumentChunks implements DocumentChunks
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function add(iterable $chunks): void
    {
        foreach ($chunks as $chunk) {
            $this->em->persist($chunk);
        }
        $this->em->flush();
    }

    public function removeForDocument(int $documentId): void
    {
        $this->em->createQuery(
            'DELETE FROM '.DocumentChunk::class.' c WHERE c.documentId = :id',
        )->setParameter('id', $documentId)->execute();
    }

    public function searchNearest(Embedding $embedding, int $limit): array
    {
        // Native query: pgvector's `<->` (L2 distance) operator has no portable
        // DQL equivalent, so we map the result rows back to DocumentChunk
        // entities via a ResultSetMapping.
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addRootEntityFromClassMetadata(DocumentChunk::class, 'c');

        $sql = sprintf(
            'SELECT %s FROM document_chunk c WHERE c.embedding IS NOT NULL '
            .'ORDER BY c.embedding <-> CAST(:vector AS vector) ASC LIMIT %d',
            $rsm->generateSelectClause(['c' => 'c']),
            $limit,
        );

        return $this->em->createNativeQuery($sql, $rsm)
            ->setParameter('vector', VectorUtils::getVectorAsString($embedding->toArray()))
            ->getResult();
    }
}
