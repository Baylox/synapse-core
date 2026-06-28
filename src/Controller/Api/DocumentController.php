<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\KnowledgeDocumentRepository;
use App\Service\Rag\DocumentUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/documents')]
final class DocumentController extends AbstractController
{
    #[Route('', name: 'api_documents_list', methods: ['GET'])]
    public function list(KnowledgeDocumentRepository $documents): JsonResponse
    {
        return $this->json([
            'documents' => array_map(
                static fn ($document) => [
                    'id' => $document->getId(),
                    'title' => $document->getTitle(),
                    'status' => $document->getStatus()->value,
                    'chunks' => $document->getChunkCount(),
                    'createdAt' => $document->getCreatedAt()->format(\DateTimeInterface::ATOM),
                ],
                $documents->findAllNewestFirst(),
            ),
        ]);
    }

    #[Route('', name: 'api_documents_upload', methods: ['POST'])]
    public function upload(Request $request, DocumentUploader $uploader): JsonResponse
    {
        $file = $request->files->get('file');

        if ($file === null) {
            return $this->json(['error' => 'Multipart field "file" is required.'], Response::HTTP_BAD_REQUEST);
        }

        $document = $uploader->upload($file);

        return $this->json([
            'id' => $document->getId(),
            'title' => $document->getTitle(),
            'status' => $document->getStatus()->value,
        ], Response::HTTP_ACCEPTED);
    }
}
