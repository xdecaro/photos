<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Value;

final class StoredPhoto
{
    public function __construct(
        public readonly string $relativePath,
        public readonly string $absolutePath,
        public readonly string $mimeType,
        public readonly int $width,
        public readonly int $height,
        public readonly int $size,
    ) {}
}
