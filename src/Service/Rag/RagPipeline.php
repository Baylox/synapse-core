<?php

declare(strict_types=1);

namespace App\Service\Rag;

use App\Entity\DocumentChunk;
use App\Repository\DocumentChunkRepository;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\EmbeddingGenerator\EmbeddingGeneratorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * The read side of the system: answer a question grounded in the knowledge
 * base (Retrieval-Augmented Generation).
 *
 *   question --> embed --> nearest chunks --> prompt(context + question) --> LLM
 *
 * We deliberately implement the retrieve-then-prompt loop ourselves (rather
 * than LLPhant's QuestionAnswering) so we control the system prompt, the
 * grounding guardrails, and the citations returned to the UI.
 */
final class RagPipeline
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
        You are Synapse, a precise assistant that answers ONLY from the provided context.
        Rules:
        - If the context does not contain the answer, say you don't know. Never invent facts.
        - Be concise and cite the source numbers you used like [1], [2].

        Context:
        %s
        PROMPT;

    public function __construct(
        private readonly EmbeddingGeneratorInterface $embeddingGenerator,
        private readonly DocumentChunkRepository $chunks,
        private readonly OpenAIChat $chat,
        #[Autowire('%env(int:RAG_RETRIEVED_CHUNKS)%')]
        private readonly int $topK = 4,
    ) {
    }

    public function answer(string $question): RagAnswer
    {
        $queryEmbedding = $this->embeddingGenerator->embedText($question);
        $chunks = $this->chunks->findNearest($queryEmbedding, $this->topK);

        if ($chunks === []) {
            return new RagAnswer(
                "I don't have any documents in my knowledge base yet, so I can't answer that.",
                [],
            );
        }

        $this->chat->setSystemMessage(sprintf(self::SYSTEM_PROMPT, $this->buildContext($chunks)));
        $answer = $this->chat->generateText($question);

        return new RagAnswer($answer, $chunks);
    }

    /** @param list<DocumentChunk> $chunks */
    private function buildContext(array $chunks): string
    {
        $blocks = [];
        foreach ($chunks as $i => $chunk) {
            $source = $chunk->getDocument()->getTitle();
            $blocks[] = sprintf("[%d] (%s)\n%s", $i + 1, $source, $chunk->getContent());
        }

        return implode("\n\n", $blocks);
    }
}
