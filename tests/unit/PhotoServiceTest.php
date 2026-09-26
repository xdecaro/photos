<?php
declare(strict_types=1);
namespace xdecaro\Core\Integration {
    final class EntityReference {
        public function __construct(private string $component, private string $entity, private string $id) {}
        public function getComponent(): string { return $this->component; }
        public function getEntity(): string { return $this->entity; }
        public function getId(): string { return $this->id; }
    }
}
namespace {
require dirname(__DIR__) . '/bootstrap.php';
foreach (['PhotoRecord.php','PhotoRepositoryInterface.php'] as $f) require dirname(__DIR__,2).'/src/com_xdecarophotos/site/src/'.($f === 'PhotoRecord.php' ? 'Value/' : 'Repository/').$f;
require dirname(__DIR__,2).'/src/com_xdecarophotos/site/src/Service/PhotoService.php';
use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Repository\PhotoRepositoryInterface;
use xdecaro\Component\Photos\Site\Service\PhotoService;
use xdecaro\Component\Photos\Site\Value\PhotoRecord;

final class FakeRepo implements PhotoRepositoryInterface {
    public array $rows=[]; public int $next=1;
    public function find(int $id): ?PhotoRecord { return $this->rows[$id] ?? null; }
    public function findByOwner(EntityReference $owner, array $filters=[]): array { return array_values(array_filter($this->rows, fn($r)=>$r->ownerComponent===$owner->getComponent() && $r->ownerEntity===$owner->getEntity() && $r->ownerId===$owner->getId() && (!isset($filters['type']) || $r->photoType===$filters['type']) && (!isset($filters['status']) || $r->status===(int)$filters['status']) && (!isset($filters['season']) || $r->season===$filters['season']))); }
    public function insert(EntityReference $owner, array $payload): PhotoRecord { $id=$this->next++; return $this->rows[$id]=PhotoRecord::fromPayload($id,$owner,$payload); }
    public function setPrimary(int $id): void { $target=$this->rows[$id]; foreach($this->rows as $k=>$r){ if($r->samePrimaryGroup($target)) $this->rows[$k]=$r->withPrimary($k===$id); } }
    public function deactivate(int $id): void { $this->rows[$id]=$this->rows[$id]->withStatus(0); }
    public function delete(int $id): void { unset($this->rows[$id]); }
}
$repo=new FakeRepo(); $service=new PhotoService($repo); $owner=new EntityReference('com_xdecaropeople','person','125');
same(null,$service->getPhoto(999),'unknown photo');
$one=$service->createPhoto($owner,['photo_type'=>'avatar','original_path'=>'originals/a.png','mime_type'=>'image/png','width'=>1,'height'=>1,'file_size'=>10,'status'=>1,'season'=>'2026']);
same('125',$one->ownerId,'owner persisted');
$two=$service->createPhoto($owner,['photo_type'=>'passport','original_path'=>'originals/b.png','mime_type'=>'image/png','width'=>1,'height'=>1,'file_size'=>10,'status'=>1,'season'=>'2026']);
same(1,count($service->getPhotos($owner,['type'=>'avatar','status'=>1,'season'=>'2026'])),'filters');
$service->setPrimaryPhoto($one->id); same($one->id,$service->getPrimaryPhoto($owner,'avatar')?->id,'primary lookup');
echo "PASS PhotoServiceTest\n";
}
