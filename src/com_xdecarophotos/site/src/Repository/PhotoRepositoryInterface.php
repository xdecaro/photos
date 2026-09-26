<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Repository;

use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Value\PhotoRecord;

interface PhotoRepositoryInterface
{
    public function find(int $id): ?PhotoRecord;
    /** @return list<PhotoRecord> */
    public function findByOwner(EntityReference $owner, array $filters = []): array;
    public function insert(EntityReference $owner, array $payload): PhotoRecord;
    public function setPrimary(int $id): void;
    public function deactivate(int $id): void;
    public function delete(int $id): void;
}
