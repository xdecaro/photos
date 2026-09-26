<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Repository;
use xdecaro\Component\Photos\Site\Value\PhotoVariant;
interface VariantRepositoryInterface
{
    public function nextId(): int;
    public function insert(PhotoVariant $variant): PhotoVariant;
    /** @return list<PhotoVariant> */ public function byPhoto(int $photoId): array;
    public function delete(int $id): void;
}
