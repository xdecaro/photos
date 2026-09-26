<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use RuntimeException;

final class StoredPhoto
{
    public function __construct(public readonly string $relativePath, public readonly string $absolutePath) {}
}

final class PhotoStorage
{
    public function __construct(private readonly string $basePath) {}
    public function storeOriginal(ValidatedImage $image): StoredPhoto
    {
        $dir = rtrim($this->basePath,'/\\').DIRECTORY_SEPARATOR.'originals';
        if (!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) throw new RuntimeException('Cannot create photo storage.');
        $name = bin2hex(random_bytes(20)).'.'.$image->extension;
        $dest = $dir.DIRECTORY_SEPARATOR.$name;
        if (!copy($image->tmpPath,$dest)) throw new RuntimeException('Cannot store original image.');
        return new StoredPhoto('originals/'.$name,$dest);
    }
    public function deleteOriginal(string $relativePath): void
    {
        $safe = str_replace('\\','/',$relativePath);
        if (!str_starts_with($safe,'originals/') || str_contains($safe,'..')) throw new RuntimeException('Invalid stored photo path.');
        $path=rtrim($this->basePath,'/\\').DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$safe);
        if (is_file($path) && !unlink($path)) throw new RuntimeException('Cannot delete original image.');
    }
    public function absolute(string $relativePath): string
    {
        if (str_contains($relativePath,'..')) throw new RuntimeException('Invalid storage path.');
        return rtrim($this->basePath,'/\\').DIRECTORY_SEPARATOR.str_replace('/',DIRECTORY_SEPARATOR,$relativePath);
    }
}
