<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Chat;

use App\Reasoning\Application\AnswerQuestionHandler;
use App\Reasoning\Application\AnswerQuestionQuery;
use App\UI\Http\Dto\Request\ChatRequest;
use App\UI\Http\Dto\Response\ChatResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class AskQuestionController extends AbstractController
{
    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] ChatRequest $request,
        AnswerQuestionHandler $answerQuestion,
    ): JsonResponse {
        $answer = $answerQuestion(new AnswerQuestionQuery($request->question, $request->hints()));

        return $this->json(ChatResponse::fromAnswer($answer));
    }
}
