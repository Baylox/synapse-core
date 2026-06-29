<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Knowledge\Domain\Repository\KnowledgeDocuments;
use App\UI\Http\Dto\Response\DocumentView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowChatController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function __invoke(KnowledgeDocuments $documents): Response
    {
        return $this->render('chat/index.html.twig', [
            'documents' => array_map(DocumentView::fromDocument(...), $documents->allNewestFirst()),
        ]);
    }
}
