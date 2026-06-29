<?php

declare(strict_types=1);

namespace App\Knowledge\Infrastructure\Storage;

use App\Knowledge\Domain\Service\FileStorage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class LocalFileStorage implements FileStorage
{
    public function __construct(
        private Filesystem $filesystem,
        private SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/%env(APP_SHARE_DIR)%')]
        private string $shareDir,
    ) {
    }

    public function store(string $temporaryPath, string $originalName): string
    {
        $base = $this->slugger->slug(pathinfo($originalName, PATHINFO_FILENAME))->lower();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION) ?: 'bin';
        $storedName = sprintf('%s-%s.%s', $base, bin2hex(random_bytes(6)), $extension);

        $this->filesystem->mkdir($this->shareDir);
        $this->filesystem->rename($temporaryPath, $this->absolutePath($storedName), true);

        return $storedName;
    }

    public function absolutePath(string $storedPath): string
    {
        return Path::isAbsolute($storedPath) ? $storedPath : Path::join($this->shareDir, $storedPath);
    }
}
