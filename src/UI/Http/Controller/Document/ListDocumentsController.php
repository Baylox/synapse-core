<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Document;

use App\Knowledge\Domain\Repository\KnowledgeDocuments;
use App\UI\Http\Dto\Response\DocumentView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ListDocumentsController extends AbstractController
{
    #[Route('/api/documents', name: 'api_documents_list', methods: ['GET'])]
    public function __invoke(KnowledgeDocuments $documents): JsonResponse
    {
        return $this->json([
            'documents' => array_map(DocumentView::fromDocument(...), $documents->allNewestFirst()),
        ]);
    }
}
