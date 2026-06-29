<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Document;

use App\Knowledge\Application\Command\UploadDocumentCommand;
use App\Knowledge\Application\UploadDocumentHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

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
        UploadDocumentHandler $uploadDocument,
    ): JsonResponse {
        $id = $uploadDocument(new UploadDocumentCommand(
            originalName: $file->getClientOriginalName(),
            mimeType: $file->getClientMimeType(),
            temporaryPath: $file->getPathname(),
        ));

        return $this->json(['id' => $id, 'status' => 'pending'], Response::HTTP_ACCEPTED);
    }
}
