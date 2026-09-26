<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use InvalidArgumentException;
use RuntimeException;
use xdecaro\Component\Photos\Site\Value\StoredPhoto;
use xdecaro\Component\Photos\Site\Value\ValidatedImage;

final class PhotoStorage
{
    private const EXTENSIONS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    public function __construct(private readonly string $basePath)
    {
        foreach (['originals', 'variants'] as $directory) {
            $path = rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $directory;
            if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
                throw new RuntimeException('Unable to create photo storage directory.');
            }
        }
    }

    public function storeOriginal(ValidatedImage $image): StoredPhoto
    {
        $extension = self::EXTENSIONS[$image->mimeType] ?? null;
        if ($extension === null) {
            throw new InvalidArgumentException('Unsupported stored image MIME type.');
        }
        $relative = 'originals/' . bin2hex(random_bytes(20)) . '.' . $extension;
        $absolute = $this->absolutePath($relative, 'originals');
        if (!copy($image->tmpPath, $absolute)) {
            throw new RuntimeException('Unable to store uploaded image.');
        }
        return new StoredPhoto($relative, $absolute, $image->mimeType, $image->width, $image->height, $image->size);
    }

    public function deleteOriginal(string $path): void
    {
        $absolute = $this->absolutePath($path, 'originals');
        if (is_file($absolute) && !unlink($absolute)) {
            throw new RuntimeException('Unable to remove original image.');
        }
    }

    public function absolutePath(string $relative, ?string $requiredPrefix = null): string
    {
        $relative = str_replace('\\', '/', trim($relative));
        if ($relative === '' || str_starts_with($relative, '/') || str_contains($relative, '../') || str_contains($relative, '/..')) {
            throw new InvalidArgumentException('Invalid photo storage path.');
        }
        if ($requiredPrefix !== null && !str_starts_with($relative, $requiredPrefix . '/')) {
            throw new InvalidArgumentException('Unexpected photo storage area.');
        }
        return rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}
