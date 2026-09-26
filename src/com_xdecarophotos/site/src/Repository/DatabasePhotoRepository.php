<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Repository;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use RuntimeException;
use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Value\PhotoRecord;

final class DatabasePhotoRepository implements PhotoRepositoryInterface
{
    public function __construct(private readonly DatabaseInterface $db) {}

    public function find(int $id): ?PhotoRecord
    {
        $q=$this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecarophotos_photos'))->where($this->db->quoteName('id').' = '.(int)$id);
        $this->db->setQuery($q); $row=$this->db->loadObject();
        return $row ? PhotoRecord::fromRow($row) : null;
    }

    public function findByOwner(EntityReference $owner, array $filters=[]): array
    {
        $q=$this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecarophotos_photos'))
            ->where($this->db->quoteName('owner_component').' = '.$this->db->quote($owner->getComponent()))
            ->where($this->db->quoteName('owner_entity').' = '.$this->db->quote($owner->getEntity()))
            ->where($this->db->quoteName('owner_id').' = '.$this->db->quote($owner->getId()));
        foreach (['type'=>'photo_type','status'=>'status','season'=>'season','is_primary'=>'is_primary'] as $key=>$column) {
            if (array_key_exists($key,$filters)) {
                $value=$filters[$key]; $q->where($this->db->quoteName($column).' = '.(is_int($value)||is_bool($value)?(int)$value:$this->db->quote((string)$value)));
            }
        }
        if (array_key_exists('context',$filters)) {
            $context=$filters['context'];
            if ($context === null) {
                $q->where($this->db->quoteName('context_component').' IS NULL')->where($this->db->quoteName('context_entity').' IS NULL')->where($this->db->quoteName('context_id').' IS NULL');
            } elseif ($context instanceof EntityReference) {
                $q->where($this->db->quoteName('context_component').' = '.$this->db->quote($context->getComponent()))
                  ->where($this->db->quoteName('context_entity').' = '.$this->db->quote($context->getEntity()))
                  ->where($this->db->quoteName('context_id').' = '.$this->db->quote($context->getId()));
            }
        }
        $q->order($this->db->quoteName('id').' DESC');
        $this->db->setQuery($q);
        return array_map([PhotoRecord::class,'fromRow'],$this->db->loadObjectList() ?: []);
    }

    public function insert(EntityReference $owner, array $payload): PhotoRecord
    {
        $context=$payload['context'] ?? null; $now=Factory::getDate()->toSql();
        $row=(object)[
            'owner_component'=>$owner->getComponent(),'owner_entity'=>$owner->getEntity(),'owner_id'=>$owner->getId(),
            'context_component'=>$context['component']??null,'context_entity'=>$context['entity']??null,'context_id'=>$context['id']??null,
            'photo_type'=>(string)($payload['photo_type']??'original'),'title'=>(string)($payload['title']??''),'description'=>(string)($payload['description']??''),
            'original_path'=>(string)($payload['original_path']??''),'mime_type'=>(string)($payload['mime_type']??''),'width'=>(int)($payload['width']??0),
            'height'=>(int)($payload['height']??0),'file_size'=>(int)($payload['file_size']??0),'orientation'=>(int)($payload['orientation']??1),
            'is_primary'=>(int)!empty($payload['is_primary']),'status'=>(int)($payload['status']??1),'season'=>$payload['season']??null,
            'created'=>$now,'created_by'=>(int)($payload['created_by']??0),'modified'=>null,'modified_by'=>0,
        ];
        $this->db->insertObject('#__xdecarophotos_photos',$row,'id');
        return $this->find((int)$row->id) ?? throw new RuntimeException('Unable to reload created photo.');
    }

    public function setPrimary(int $id): void
    {
        $target=$this->find($id) ?? throw new RuntimeException('Photo not found.');
        $this->db->transactionStart();
        try {
            $q=$this->db->getQuery(true)->update($this->db->quoteName('#__xdecarophotos_photos'))->set($this->db->quoteName('is_primary').' = 0')
                ->where($this->db->quoteName('owner_component').' = '.$this->db->quote($target->ownerComponent))
                ->where($this->db->quoteName('owner_entity').' = '.$this->db->quote($target->ownerEntity))
                ->where($this->db->quoteName('owner_id').' = '.$this->db->quote($target->ownerId))
                ->where($this->db->quoteName('photo_type').' = '.$this->db->quote($target->photoType));
            foreach (['context_component'=>$target->contextComponent,'context_entity'=>$target->contextEntity,'context_id'=>$target->contextId] as $column=>$value) {
                $q->where($this->db->quoteName($column).($value===null?' IS NULL':' = '.$this->db->quote($value)));
            }
            $this->db->setQuery($q)->execute();
            $q=$this->db->getQuery(true)->update($this->db->quoteName('#__xdecarophotos_photos'))->set($this->db->quoteName('is_primary').' = 1')->where($this->db->quoteName('id').' = '.$id);
            $this->db->setQuery($q)->execute(); $this->db->transactionCommit();
        } catch (\Throwable $e) { $this->db->transactionRollback(); throw $e; }
    }

    public function deactivate(int $id): void { $q=$this->db->getQuery(true)->update($this->db->quoteName('#__xdecarophotos_photos'))->set($this->db->quoteName('status').' = 0')->where($this->db->quoteName('id').' = '.$id); $this->db->setQuery($q)->execute(); }
    public function delete(int $id): void { $q=$this->db->getQuery(true)->delete($this->db->quoteName('#__xdecarophotos_photos'))->where($this->db->quoteName('id').' = '.$id); $this->db->setQuery($q)->execute(); }
}
