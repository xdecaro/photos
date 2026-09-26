<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use InvalidArgumentException;

final class PhotoDeletionService
{
    public function __construct(private readonly object $photos,private readonly object $variants) {}
    public function deactivate(int $photoId): void { $this->photos->deactivatePhoto($photoId); }
    public function deleteVariant(int $variantId,string $path): void
    {
        if($path!=='' && is_file($path) && !unlink($path)) throw new InvalidArgumentException('Unable to delete variant file.');
        $this->variants->delete($variantId);
    }
    public function deletePhoto(int $photoId,string $originalPath): void
    {
        foreach($this->variants->byPhoto($photoId) as $variant){ if(is_file($variant->path) && !unlink($variant->path)) throw new InvalidArgumentException('Unable to delete variant file.'); $this->variants->delete($variant->id); }
        if(is_file($originalPath) && !unlink($originalPath)) throw new InvalidArgumentException('Unable to delete original file.');
        $this->photos->deletePhoto($photoId);
    }
}
