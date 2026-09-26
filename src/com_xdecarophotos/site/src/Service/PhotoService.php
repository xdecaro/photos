<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Value\PhotoRecord;
final class PhotoService
{
    public function __construct(private readonly PhotoRepository $repository) {}
    public function getPhoto(int $photoId): ?PhotoRecord { return $this->repository->find($photoId); }
    public function getPhotos(EntityReference $owner, array $filters=[]): array { return $this->repository->findByOwner($owner,$filters); }
    public function getPrimaryPhoto(EntityReference $owner,string $type,?EntityReference $context=null): ?PhotoRecord
    {
        $filters=['type'=>$type,'primary'=>true]; if($context) $filters['context']=$context;
        return $this->repository->findByOwner($owner,$filters)[0]??null;
    }
    public function createPhoto(EntityReference $owner,array $payload): PhotoRecord { return $this->repository->create($owner,$payload); }
    public function setPrimaryPhoto(int $photoId): void { $this->repository->setPrimary($photoId); }
}
