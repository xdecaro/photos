<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Repository\PhotoRepositoryInterface;
use xdecaro\Component\Photos\Site\Value\PhotoRecord;

final class PhotoService
{
    public function __construct(private readonly PhotoRepositoryInterface $repository) {}
    public function getPhoto(int $photoId): ?PhotoRecord { return $this->repository->find($photoId); }
    public function getPhotos(EntityReference $owner,array $filters=[]): array { return $this->repository->findByOwner($owner,$filters); }
    public function getPrimaryPhoto(EntityReference $owner,string $type,?EntityReference $context=null): ?PhotoRecord
    {
        $rows=$this->repository->findByOwner($owner,['type'=>$type,'context'=>$context,'is_primary'=>1,'status'=>1]);
        foreach($rows as $row){ if($row->isPrimary) return $row; }
        return null;
    }
    public function createPhoto(EntityReference $owner,array $payload): PhotoRecord { return $this->repository->insert($owner,$payload); }
    public function setPrimaryPhoto(int $photoId): void { $this->repository->setPrimary($photoId); }
    public function deactivatePhoto(int $photoId): void { $this->repository->deactivate($photoId); }
    public function deletePhoto(int $photoId): void { $this->repository->delete($photoId); }
}
