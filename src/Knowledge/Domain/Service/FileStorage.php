<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Service;

/**
 * Port: where original uploaded files live. The domain only deals in opaque
 * stored paths; the adapter decides if that's local disk, S3, etc.
 */
interface FileStorage
{
    /** @return string the stored path (relative, opaque to the domain) */
    public function store(string $temporaryPath, string $originalName): string;

    public function absolutePath(string $storedPath): string;
}
