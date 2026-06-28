<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\KnowledgeDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Renders the chat UI. Single-action (invokable) controller.
 */
final class ShowChatController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function __invoke(KnowledgeDocumentRepository $documents): Response
    {
        return $this->render('chat/index.html.twig', [
            'documents' => $documents->findAllNewestFirst(),
        ]);
    }
}
