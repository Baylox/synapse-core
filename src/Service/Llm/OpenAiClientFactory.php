<?php

declare(strict_types=1);

namespace App\Service\Llm;

use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\EmbeddingGenerator\EmbeddingGeneratorInterface;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3LargeEmbeddingGenerator;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAIADA002EmbeddingGenerator;
use LLPhant\OpenAIConfig;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Single place that turns our environment configuration into ready-to-use
 * LLPhant clients. Registered as a factory in config/services.yaml so the
 * rest of the app can type-hint {@see OpenAIChat} and
 * {@see EmbeddingGeneratorInterface} directly.
 */
final class OpenAiClientFactory
{
    public function __construct(
        #[Autowire('%env(OPENAI_API_KEY)%')]
        private readonly string $apiKey,
        #[Autowire('%env(OPENAI_CHAT_MODEL)%')]
        private readonly string $chatModel,
        #[Autowire('%env(OPENAI_EMBEDDING_MODEL)%')]
        private readonly string $embeddingModel,
    ) {
    }

    public function createChatModel(): OpenAIChat
    {
        return new OpenAIChat($this->config($this->chatModel));
    }

    public function createEmbeddingGenerator(): EmbeddingGeneratorInterface
    {
        $config = $this->config($this->embeddingModel);

        return match ($this->embeddingModel) {
            'text-embedding-3-large' => new OpenAI3LargeEmbeddingGenerator($config),
            'text-embedding-ada-002' => new OpenAIADA002EmbeddingGenerator($config),
            // text-embedding-3-small (1536 dims) is our default; see DocumentChunk::EMBEDDING_DIMENSIONS.
            default => new OpenAI3SmallEmbeddingGenerator($config),
        };
    }

    private function config(string $model): OpenAIConfig
    {
        $config = new OpenAIConfig();
        $config->apiKey = $this->apiKey;
        $config->model = $model;

        return $config;
    }
}
