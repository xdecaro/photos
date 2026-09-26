<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
interface DeletionRepository { public function deactivate(int $photoId): void; public function deleteVariantsForPhoto(int $photoId): array; public function deletePhotoRow(int $photoId): void; }
final class DeletionService
{
    public function __construct(private readonly DeletionRepository $repo) {}
    public function deactivate(int $photoId): void { $this->repo->deactivate($photoId); }
    public function deletePermanently(int $photoId): array { $files=$this->repo->deleteVariantsForPhoto($photoId); $this->repo->deletePhotoRow($photoId); return $files; }
}
