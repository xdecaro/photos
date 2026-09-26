<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Value\PhotoRecord;
interface PhotoRepository
{
    public function find(int $id): ?PhotoRecord;
    public function findByOwner(EntityReference $owner, array $filters=[]): array;
    public function create(EntityReference $owner, array $payload): PhotoRecord;
    public function setPrimary(int $id): void;
}
