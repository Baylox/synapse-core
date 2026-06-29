<?php

declare(strict_types=1);

namespace App\Reasoning\Domain\Strategy;

use App\Reasoning\Domain\Model\Answer;
use App\Reasoning\Domain\Model\Question;

/**
 * The core seam for the "AI reasons depending on the input" requirement.
 * Each strategy declares the questions it can handle and produces an Answer.
 * Examples: plain RAG, agentic tool-use, multi-step planning, a domain-specific
 * reasoner for contracts vs invoices, etc.
 */
interface ReasoningStrategy
{
    public function supports(Question $question): bool;

    public function reason(Question $question): Answer;
}
