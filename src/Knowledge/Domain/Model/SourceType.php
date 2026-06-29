<?php

declare(strict_types=1);

namespace App\Knowledge\Domain\Model;

/**
 * The kind of source a document came from. Drives which IngestionStrategy is
 * used (multi-source seam).
 */
enum SourceType: string
{
    case Pdf = 'pdf';
    case Word = 'word';
    case Text = 'text';
    case Unknown = 'unknown';

    public static function fromMimeType(string $mimeType): self
    {
        return match ($mimeType) {
            'application/pdf' => self::Pdf,
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => self::Word,
            'text/plain', 'text/markdown' => self::Text,
            default => self::Unknown,
        };
    }
}
