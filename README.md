# Synapse Core

A **Retrieval-Augmented Generation (RAG)** core built on Symfony 7.4 + LLPhant.
Upload documents (PDF, Word, text…), and ask questions that are answered
**grounded in your own content** rather than the model's general knowledge.

> Status: foundational skeleton + architecture. The pipeline is wired
> end-to-end; tune prompts, add auth, and enrich the UI as you grow.

## How it works

```
                 ┌─────────────── WRITE side (ingestion) ───────────────┐
 upload file ──▶ DocumentUploader ──▶ [async] IngestDocument ──▶ DocumentIngestor
                 store + KnowledgeDocument(pending)        read → split → embed → DocumentChunk(+vector)

                 ┌─────────────── READ side (querying) ─────────────────┐
 question ─────▶ RagPipeline ──▶ embed question ──▶ DocumentChunkRepository::findNearest (pgvector)
                              └──▶ build context + system prompt ──▶ OpenAIChat ──▶ grounded answer + citations
```

### Key components (`src/`)

| Layer | Class | Responsibility |
|-------|-------|----------------|
| Entity | `KnowledgeDocument` | A source file and its ingestion lifecycle (`DocumentStatus`). |
| Entity | `DocumentChunk` | An embedded slice stored in a pgvector `vector(1536)` column. |
| Entity | `Conversation` / `ChatMessage` | Chat history (with optional source citations). |
| Service | `Llm\OpenAiClientFactory` | Builds LLPhant `OpenAIChat` + embedding generator from env. |
| Service | `Rag\DocumentUploader` | Stores an upload, then dispatches async ingestion. |
| Service | `Rag\DocumentIngestor` | read → split → embed → persist chunks. |
| Service | `Rag\RagPipeline` | embed query → retrieve → prompt → answer (`RagAnswer`). |
| Messaging | `Message\IngestDocument` + handler | Runs ingestion off the request (Messenger). |
| HTTP | `Controller\HomeController` | Chat UI. |
| HTTP | `Controller\Api\ChatController` | `POST /api/chat`. |
| HTTP | `Controller\Api\DocumentController` | `GET/POST /api/documents`. |

Vector search reuses LLPhant's Doctrine integration: the `vector` column type
and the `L2_DISTANCE` DQL function (registered in `config/packages/doctrine.yaml`).

## Setup

1. **Configure secrets** — create `.env.local` (gitignored):
   ```dotenv
   OPENAI_API_KEY=sk-...
   ```
   Other knobs live in `.env`: `OPENAI_CHAT_MODEL` (default `gpt-4o-mini`),
   `OPENAI_EMBEDDING_MODEL` (`text-embedding-3-small`), `RAG_CHUNK_SIZE`,
   `RAG_RETRIEVED_CHUNKS`.

2. **Start PostgreSQL** (needs the `pgvector` extension — use the
   `pgvector/pgvector:pg16` image, or install the extension in your DB):
   ```bash
   docker compose up -d database
   ```

3. **Install & build**:
   ```bash
   composer install
   npm install && npm run build
   ```

4. **Create the schema**:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Run**:
   ```bash
   symfony serve            # or: php -S localhost:8000 -t public
   php bin/console messenger:consume async -vv   # ingestion worker
   ```

Open <http://localhost:8000>, upload a document, and start asking questions.

## Notes / next steps

- **Embedding dimension** is pinned to 1536 (`DocumentChunk::EMBEDDING_DIMENSIONS`).
  Changing the embedding model means a new migration that re-creates the column.
- **Security**: the firewall is currently open (`config/packages/security.yaml`).
  Add real authentication before exposing this beyond local use.
- The chat is stateless today; `Conversation`/`ChatMessage` are in place to make
  it conversation-aware (history + follow-ups) next.

## Tests

```bash
php bin/phpunit
```
