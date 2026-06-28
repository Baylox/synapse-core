<?php

declare(strict_types=1);

namespace App\Controller\Document;

use App\Dto\Response\DocumentView;
use App\Service\Rag\DocumentUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * POST /api/documents — accept a file and schedule async ingestion.
 *
 * The file is validated declaratively via #[MapUploadedFile] (wrong type or
 * oversized → 422) before the controller body runs.
 */
final class UploadDocumentController extends AbstractController
{
    #[Route('/api/documents', name: 'api_documents_upload', methods: ['POST'])]
    public function __invoke(
        #[MapUploadedFile(
            constraints: [
                new Assert\File(
                    maxSize: '20M',
                    mimeTypes: [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/plain',
                    ],
                    mimeTypesMessage: 'Please upload a PDF, Word or text document.',
                ),
            ],
            name: 'file',
        )]
        UploadedFile $file,
        DocumentUploader $uploader,
    ): JsonResponse {
        return $this->json(
            DocumentView::fromEntity($uploader->upload($file)),
            Response::HTTP_ACCEPTED,
        );
    }
}
