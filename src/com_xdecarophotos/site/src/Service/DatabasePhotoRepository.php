<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
use Joomla\Database\DatabaseInterface;
use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Value\PhotoRecord;
use InvalidArgumentException;
final class DatabasePhotoRepository implements PhotoRepository
{
    public function __construct(private readonly DatabaseInterface $db) {}
    public function find(int $id): ?PhotoRecord
    {
        $q=$this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecarophotos_photos'))->where($this->db->quoteName('id').'='.(int)$id); $this->db->setQuery($q); $row=$this->db->loadAssoc(); return $row?$this->map($row):null;
    }
    public function findByOwner(EntityReference $owner,array $filters=[]): array
    {
        $q=$this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecarophotos_photos'))
          ->where($this->db->quoteName('owner_component').'='.$this->db->quote($owner->component))->where($this->db->quoteName('owner_entity').'='.$this->db->quote($owner->entity))->where($this->db->quoteName('owner_id').'='.$this->db->quote((string)$owner->id));
        if(isset($filters['type']))$q->where($this->db->quoteName('photo_type').'='.$this->db->quote((string)$filters['type'])); if(isset($filters['status']))$q->where($this->db->quoteName('status').'='.(int)$filters['status']); if(!empty($filters['primary']))$q->where($this->db->quoteName('is_primary').'=1'); if(isset($filters['season']))$q->where($this->db->quoteName('season').'='.$this->db->quote((string)$filters['season']));
        if(isset($filters['context'])&&$filters['context'] instanceof EntityReference){$c=$filters['context'];$q->where($this->db->quoteName('context_component').'='.$this->db->quote($c->component))->where($this->db->quoteName('context_entity').'='.$this->db->quote($c->entity))->where($this->db->quoteName('context_id').'='.$this->db->quote((string)$c->id));}
        $this->db->setQuery($q); return array_map(fn($r)=>$this->map((array)$r),$this->db->loadAssocList()?:[]);
    }
    public function create(EntityReference $owner,array $payload): PhotoRecord
    {
        $ctx=$payload['context']??null; $row=(object)['owner_component'=>$owner->component,'owner_entity'=>$owner->entity,'owner_id'=>(string)$owner->id,'context_component'=>$ctx?->component,'context_entity'=>$ctx?->entity,'context_id'=>$ctx?(string)$ctx->id:null,'photo_type'=>(string)($payload['photo_type']??'original'),'original_path'=>(string)($payload['original_path']??''),'mime_type'=>(string)($payload['mime_type']??''),'width'=>(int)($payload['width']??0),'height'=>(int)($payload['height']??0),'file_size'=>(int)($payload['file_size']??0),'orientation'=>(int)($payload['orientation']??1),'is_primary'=>(int)($payload['is_primary']??0),'status'=>(int)($payload['status']??1),'season'=>$payload['season']??null,'created'=>date('Y-m-d H:i:s'),'created_by'=>(int)($payload['created_by']??0)]; $this->db->insertObject('#__xdecarophotos_photos',$row); $id=(int)$this->db->insertid(); return $this->find($id)??throw new \RuntimeException('Photo insert failed.');
    }
    public function setPrimary(int $id): void
    {
        $target=$this->find($id)??throw new InvalidArgumentException('Photo not found.'); $this->db->transactionStart(); try { $conditions=['owner_component='.$this->db->quote($target->ownerComponent),'owner_entity='.$this->db->quote($target->ownerEntity),'owner_id='.$this->db->quote($target->ownerId),'photo_type='.$this->db->quote($target->photoType)]; if($target->contextComponent===null){$conditions[]='context_component IS NULL';$conditions[]='context_entity IS NULL';$conditions[]='context_id IS NULL';}else{$conditions[]='context_component='.$this->db->quote($target->contextComponent);$conditions[]='context_entity='.$this->db->quote((string)$target->contextEntity);$conditions[]='context_id='.$this->db->quote((string)$target->contextId);} $q=$this->db->getQuery(true)->update($this->db->quoteName('#__xdecarophotos_photos'))->set($this->db->quoteName('is_primary').'=0')->where($conditions);$this->db->setQuery($q)->execute();$q=$this->db->getQuery(true)->update($this->db->quoteName('#__xdecarophotos_photos'))->set($this->db->quoteName('is_primary').'=1')->where($this->db->quoteName('id').'='.(int)$id);$this->db->setQuery($q)->execute();$this->db->transactionCommit(); } catch(\Throwable $e){$this->db->transactionRollback();throw $e;}
    }
    private function map(array $r): PhotoRecord { return new PhotoRecord((int)$r['id'],(string)$r['owner_component'],(string)$r['owner_entity'],(string)$r['owner_id'],$r['context_component']!==null?(string)$r['context_component']:null,$r['context_entity']!==null?(string)$r['context_entity']:null,$r['context_id']!==null?(string)$r['context_id']:null,(string)$r['photo_type'],(string)$r['original_path'],(string)$r['mime_type'],(bool)$r['is_primary'],(int)$r['status'],$r['season']!==null?(string)$r['season']:null); }
}
