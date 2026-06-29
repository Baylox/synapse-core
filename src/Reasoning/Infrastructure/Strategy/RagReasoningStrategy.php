<?php

declare(strict_types=1);

namespace App\Reasoning\Infrastructure\Strategy;

use App\Reasoning\Domain\Model\Answer;
use App\Reasoning\Domain\Model\Question;
use App\Reasoning\Domain\Model\RetrievedPassage;
use App\Reasoning\Domain\Port\ChatModel;
use App\Reasoning\Domain\Port\KnowledgeRetriever;
use App\Reasoning\Domain\Strategy\ReasoningStrategy;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Default strategy: retrieve relevant passages and answer strictly from them.
 */
final readonly class RagReasoningStrategy implements ReasoningStrategy
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
        You are Synapse, a precise assistant that answers ONLY from the provided context.
        - If the context does not contain the answer, say you don't know. Never invent facts.
        - Be concise and cite the source numbers you used like [1], [2].

        Context:
        %s
        PROMPT;

    public function __construct(
        private KnowledgeRetriever $retriever,
        private ChatModel $chat,
        #[Autowire('%env(int:RAG_RETRIEVED_CHUNKS)%')]
        private int $topK = 4,
    ) {
    }

    public function supports(Question $question): bool
    {
        return true; // fallback default
    }

    public function reason(Question $question): Answer
    {
        $passages = $this->retriever->retrieve($question, $this->topK);

        if ($passages === []) {
            return new Answer(
                "I don't have any documents in my knowledge base yet, so I can't answer that.",
                [],
                ['retrieval: 0 passages'],
            );
        }

        $systemPrompt = sprintf(self::SYSTEM_PROMPT, $this->buildContext($passages));
        $text = $this->chat->complete($systemPrompt, $question->text);

        return new Answer(
            $text,
            array_map(static fn (RetrievedPassage $p): \App\Reasoning\Domain\Model\Citation => $p->toCitation(), $passages),
            [sprintf('retrieval: %d passages', \count($passages)), 'strategy: rag'],
        );
    }

    /** @param list<RetrievedPassage> $passages */
    private function buildContext(array $passages): string
    {
        $blocks = [];
        foreach ($passages as $i => $passage) {
            $blocks[] = sprintf("[%d] (%s)\n%s", $i + 1, $passage->documentTitle, $passage->content);
        }

        return implode("\n\n", $blocks);
    }
}
