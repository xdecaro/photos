<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
use Joomla\Database\DatabaseInterface;
use xdecaro\Component\Photos\Site\Value\PhotoVariant;
final class DatabaseVariantRepository implements VariantRepository
{
    public function __construct(private readonly DatabaseInterface $db) {}
    public function save(PhotoVariant $variant): PhotoVariant
    {
        $row=(object)['photo_id'=>$variant->photoId,'preset'=>$variant->preset,'path'=>$variant->path,'mime_type'=>$variant->mimeType,'width'=>$variant->width,'height'=>$variant->height,'crop_x'=>$variant->cropX,'crop_y'=>$variant->cropY,'crop_width'=>$variant->cropWidth,'crop_height'=>$variant->cropHeight,'rotation'=>$variant->rotation,'generated'=>date('Y-m-d H:i:s'),'checksum'=>$variant->checksum];
        $this->db->insertObject('#__xdecarophotos_variants',$row);
        return $variant;
    }
    public function deleteByPhoto(int $photoId): array
    {
        $q=$this->db->getQuery(true)->select($this->db->quoteName('path'))->from($this->db->quoteName('#__xdecarophotos_variants'))->where($this->db->quoteName('photo_id').'='.(int)$photoId); $this->db->setQuery($q); $paths=$this->db->loadColumn()?:[];
        $q=$this->db->getQuery(true)->delete($this->db->quoteName('#__xdecarophotos_variants'))->where($this->db->quoteName('photo_id').'='.(int)$photoId); $this->db->setQuery($q)->execute(); return array_map('strval',$paths);
    }
}
