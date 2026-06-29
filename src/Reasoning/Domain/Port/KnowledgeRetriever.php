<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Port;

use App\Reasoning\Domain\Model\Question;
use App\Reasoning\Domain\Model\RetrievedPassage;

/**
 * Port: fetch passages relevant to a question. Implemented by an adapter that
 * bridges to the Knowledge context (anti-corruption boundary between contexts).
 *
 * @method list<RetrievedPassage> retrieve(Question $question, int $limit)
 */
interface KnowledgeRetriever
{
    /** @return list<RetrievedPassage> */
    public function retrieve(Question $question, int $limit): array;
}
