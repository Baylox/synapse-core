<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Dto\Request\ChatRequest;
use App\Dto\Response\ChatResponse;
use App\Service\Rag\RagPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

/**
 * POST /api/chat — answer a question grounded in the knowledge base.
 *
 * Input is validated declaratively via #[MapRequestPayload] (invalid payload
 * → 422). The controller only maps DTO → service → DTO.
 */
final class AskQuestionController extends AbstractController
{
    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] ChatRequest $request,
        RagPipeline $rag,
    ): JsonResponse {
        return $this->json(ChatResponse::fromRagAnswer($rag->answer($request->question)));
    }
}
