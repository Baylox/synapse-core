<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Repository;

use App\Knowledge\Domain\Model\KnowledgeDocument;

/**
 * Port: persistence boundary for the KnowledgeDocument aggregate.
 */
interface KnowledgeDocuments
{
    public function save(KnowledgeDocument $document): void;

    public function ofId(int $id): ?KnowledgeDocument;

    /** @return list<KnowledgeDocument> */
    public function allNewestFirst(): array;
}
