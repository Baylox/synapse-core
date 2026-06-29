<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Llm;

use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\EmbeddingGenerator\EmbeddingGeneratorInterface;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3LargeEmbeddingGenerator;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAIADA002EmbeddingGenerator;
use LLPhant\OpenAIConfig;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Builds LLPhant OpenAI clients from environment configuration. Registered as a
 * factory so adapters can type-hint OpenAIChat / EmbeddingGeneratorInterface.
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
