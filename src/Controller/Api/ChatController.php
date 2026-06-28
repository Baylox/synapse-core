<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Rag\RagPipeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/chat')]
final class ChatController extends AbstractController
{
    #[Route('', name: 'api_chat', methods: ['POST'])]
    public function chat(Request $request, RagPipeline $rag): JsonResponse
    {
        /** @var array{question?: mixed} $payload */
        $payload = json_decode($request->getContent() ?: '{}', true) ?? [];
        $question = is_string($payload['question'] ?? null) ? trim($payload['question']) : '';

        if ($question === '') {
            return $this->json(['error' => 'A non-empty "question" is required.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $rag->answer($question);

        return $this->json([
            'answer' => $result->answer,
            'grounded' => $result->isGrounded(),
            'sources' => array_map(
                static fn ($chunk) => [
                    'id' => $chunk->getId(),
                    'document' => $chunk->getDocument()->getTitle(),
                    'excerpt' => mb_substr($chunk->getContent(), 0, 200),
                ],
                $result->sources,
            ),
        ]);
    }
}
