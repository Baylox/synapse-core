<?php

declare(strict_types=1);

namespace App\Service\Rag;

use App\Entity\DocumentChunk;
use App\Entity\KnowledgeDocument;
use Doctrine\ORM\EntityManagerInterface;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingGenerator\EmbeddingGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Path;

/**
 * The write side of the system: turn an uploaded file into searchable,
 * embedded chunks.
 *
 *   file --> read text --> split --> embed --> persist DocumentChunk rows
 *
 * Designed to run inside a Messenger worker (see IngestDocumentHandler) so a
 * large PDF never blocks the HTTP request.
 */
final class DocumentIngestor
{
    public function __construct(
        private readonly EmbeddingGeneratorInterface $embeddingGenerator,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
        #[Autowire('%env(int:RAG_CHUNK_SIZE)%')]
        private readonly int $chunkSize = 1000,
        #[Autowire('%kernel.project_dir%/%env(APP_SHARE_DIR)%')]
        private readonly string $shareDir = '',
    ) {
    }

    public function ingest(KnowledgeDocument $document): void
    {
        $document->markProcessing();
        $this->em->flush();

        try {
            $path = $this->resolvePath($document->getSourcePath());

            $rawDocuments = (new FileDataReader($path))->getDocuments();
            $splitDocuments = DocumentSplitter::splitDocuments($rawDocuments, $this->chunkSize);
            $embedded = $this->embeddingGenerator->embedDocuments($splitDocuments);

            foreach ($embedded as $index => $llDocument) {
                $chunk = new DocumentChunk($document, $llDocument->content, $index);
                if (\is_array($llDocument->embedding)) {
                    $chunk->setEmbedding($llDocument->embedding);
                }
                $this->em->persist($chunk);
            }

            $document->markIngested(\count($embedded));
            $this->em->flush();

            $this->logger->info('Ingested document', [
                'document' => $document->getId(),
                'chunks' => $document->getChunkCount(),
            ]);
        } catch (\Throwable $exception) {
            $document->markFailed($exception->getMessage());
            $this->em->flush();
            $this->logger->error('Document ingestion failed', [
                'document' => $document->getId(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function resolvePath(string $sourcePath): string
    {
        return Path::isAbsolute($sourcePath)
            ? $sourcePath
            : Path::join($this->shareDir, $sourcePath);
    }
}
