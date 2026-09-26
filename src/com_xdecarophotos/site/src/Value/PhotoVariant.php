<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Value;

final class PhotoVariant
{
    public function __construct(
        public readonly int $id,
        public readonly int $photoId,
        public readonly string $preset,
        public readonly string $path,
        public readonly string $mimeType,
        public readonly int $width,
        public readonly int $height,
        public readonly float $cropX,
        public readonly float $cropY,
        public readonly float $cropWidth,
        public readonly float $cropHeight,
        public readonly int $rotation,
        public readonly string $checksum,
    ) {}
}
