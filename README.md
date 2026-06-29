# Synapse Core

A **Retrieval-Augmented Generation (RAG)** core built on Symfony 7.4 + LLPhant.
Upload documents (PDF, Word, text…), and ask questions that are answered
**grounded in your own content** rather than the model's general knowledge.

> Status: foundational architecture. Wired end-to-end and validated against a
> real pgvector database (persistence, embedding round-trip and nearest-neighbour
> search all confirmed). Designed to absorb growing AI complexity.

## Architecture — hexagonal, by bounded context

The domain is **pure PHP** (no framework or ORM annotations). Each context is
split into `Domain` (models, ports, events), `Application` (use cases), and
`Infrastructure` (adapters). Dependencies point inward; the outside world plugs
in through ports.

```
src/
├── Shared/        Embedding VO · DomainEvent + dispatcher · EmbeddingType (Doctrine) · OpenAI factory
├── Knowledge/     documents & chunks, ingestion (multi-source)
│   ├── Domain/         KnowledgeDocument (aggregate, emits events), DocumentChunk,
│   │                   ports: KnowledgeDocuments, DocumentChunks, Embedder, Reader,
│   │                   Splitter, IngestionStrategy/-ies, FileStorage
│   ├── Application/    UploadDocumentHandler, IngestDocumentHandler
│   └── Infrastructure/ Doctrine (XML mapping) · LLPhant adapters · ingestion strategies · Messenger
├── Reasoning/     the AI "thinking" — where complexity will grow
│   ├── Domain/         Question, Answer, Citation; ports: ChatModel, KnowledgeRetriever,
│   │                   Tool/ToolBox; ReasoningStrategy/-ies
│   ├── Application/    AnswerQuestionHandler
│   └── Infrastructure/ RagReasoningStrategy (default) · AgenticReasoningStrategy (tool-loop seam)
│                       · LlphantChatModel · KnowledgeBaseRetriever (cross-context ACL)
└── UI/            driving adapters: HTTP controllers (1 action each) + request/response DTOs
```

### Seams for the four complexity axes

| Axis | Seam |
|------|------|
| **Agentic orchestration** | `Reasoning\Domain\Port\Tool` + `ToolBox` + `AgenticReasoningStrategy` (ReAct loop scaffolded). |
| **Strategy per input** | `ReasoningStrategy::supports(Question)` + `ReasoningStrategies` resolver (priority-ordered). |
| **Rich business rules** | Aggregates (`KnowledgeDocument`) own invariants & emit `DomainEvent`s. |
| **Multi-source / multi-base** | `IngestionStrategy::supports(SourceType)` + `IngestionStrategies` registry; repositories are ports. |

A request flows: **HTTP DTO → Application use case → Domain (via ports) →
Infrastructure adapters**. Adding a tool, an ingestion strategy, or a reasoning
strategy is a new class tagged for its registry — no change to application or
domain code.

Persistence keeps the domain pure via XML mapping in each context's
`Infrastructure/Doctrine/mapping`, and a custom `EmbeddingType` maps the
`Embedding` value object onto a pgvector `vector(1536)` column. Nearest-neighbour
search uses a native pgvector `<->` query in `DoctrineDocumentChunks`.

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

- **Embedding dimension** is pinned to 1536 (`EmbeddingType::DEFAULT_DIMENSIONS`
  + the mapping length). Changing the embedding model means a new migration that
  re-creates the column.
- **Security**: the firewall is currently open (`config/packages/security.yaml`).
  Add real authentication before exposing this beyond local use.
- **Conversation** is a future bounded context (stateful chat / follow-ups); the
  `Reasoning` ports already accommodate it.
- Fill in `AgenticReasoningStrategy` (the ReAct loop) and add concrete `Tool`s as
  the agentic requirements firm up.

## Tests

```bash
php bin/phpunit
```
