<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Repository;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use xdecaro\Component\Photos\Site\Value\PhotoVariant;

final class DatabaseVariantRepository implements VariantRepositoryInterface
{
    public function __construct(private readonly DatabaseInterface $db) {}
    public function nextId(): int { return 0; }
    public function insert(PhotoVariant $v): PhotoVariant
    {
        $row=(object)['photo_id'=>$v->photoId,'preset'=>$v->preset,'path'=>$v->path,'mime_type'=>$v->mimeType,'width'=>$v->width,'height'=>$v->height,'crop_x'=>$v->cropX,'crop_y'=>$v->cropY,'crop_width'=>$v->cropWidth,'crop_height'=>$v->cropHeight,'rotation'=>$v->rotation,'generated'=>Factory::getDate()->toSql(),'checksum'=>$v->checksum];
        $this->db->insertObject('#__xdecarophotos_variants',$row,'id');
        return new PhotoVariant((int)$row->id,$v->photoId,$v->preset,$v->path,$v->mimeType,$v->width,$v->height,$v->cropX,$v->cropY,$v->cropWidth,$v->cropHeight,$v->rotation,$v->checksum);
    }
    public function byPhoto(int $photoId): array
    {
        $q=$this->db->getQuery(true)->select('*')->from($this->db->quoteName('#__xdecarophotos_variants'))->where($this->db->quoteName('photo_id').' = '.$photoId); $this->db->setQuery($q);
        return array_map(static fn($r)=>new PhotoVariant((int)$r->id,(int)$r->photo_id,(string)$r->preset,(string)$r->path,(string)$r->mime_type,(int)$r->width,(int)$r->height,(float)$r->crop_x,(float)$r->crop_y,(float)$r->crop_width,(float)$r->crop_height,(int)$r->rotation,(string)($r->checksum??'')),$this->db->loadObjectList()?:[]);
    }
    public function delete(int $id): void { $q=$this->db->getQuery(true)->delete($this->db->quoteName('#__xdecarophotos_variants'))->where($this->db->quoteName('id').' = '.$id);$this->db->setQuery($q)->execute(); }
}
