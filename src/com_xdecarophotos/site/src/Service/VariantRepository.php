<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
use xdecaro\Component\Photos\Site\Value\PhotoVariant;
interface VariantRepository { public function save(PhotoVariant $variant): PhotoVariant; public function deleteByPhoto(int $photoId): array; }
