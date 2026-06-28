<?php

declare(strict_types=1);

namespace App\Controller\Document;

use App\Dto\Response\DocumentView;
use App\Repository\KnowledgeDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * GET /api/documents — list the knowledge base, newest first.
 */
final class ListDocumentsController extends AbstractController
{
    #[Route('/api/documents', name: 'api_documents_list', methods: ['GET'])]
    public function __invoke(KnowledgeDocumentRepository $documents): JsonResponse
    {
        return $this->json([
            'documents' => array_map(
                DocumentView::fromEntity(...),
                $documents->findAllNewestFirst(),
            ),
        ]);
    }
}
