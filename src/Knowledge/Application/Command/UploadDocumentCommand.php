<?php

declare(strict_types=1);

namespace App\Knowledge\Application\Command;

final readonly class UploadDocumentCommand
{
    public function __construct(
        public string $originalName,
        public string $mimeType,
        public string $temporaryPath,
    ) {
    }
}
