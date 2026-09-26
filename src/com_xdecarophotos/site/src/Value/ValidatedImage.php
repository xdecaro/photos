<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Value;

final class ValidatedImage
{
    public function __construct(
        public readonly string $tmpPath,
        public readonly string $originalName,
        public readonly string $mimeType,
        public readonly int $width,
        public readonly int $height,
        public readonly int $size,
        public readonly int $orientation = 1,
    ) {}
}
