<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Model;
use Joomla\CMS\MVC\Model\ListModel;
final class PhotosModel extends ListModel
{
    protected function populateState($ordering=null,$direction=null): void
    {
        parent::populateState('p.created','DESC');
        foreach(['owner_component','owner_entity','owner_id','photo_type','context_component','context_entity','context_id','season','status','is_primary'] as $key) {
            $this->setState('filter.'.$key,$this->getUserStateFromRequest($this->context.'.filter.'.$key,'filter_'.$key));
        }
    }
    protected function getListQuery()
    {
        $db=$this->getDatabase(); $q=$db->getQuery(true)->select('p.*')->from($db->quoteName('#__xdecarophotos_photos','p'));
        foreach(['owner_component','owner_entity','owner_id','photo_type','context_component','context_entity','context_id','season'] as $key){$v=(string)$this->getState('filter.'.$key); if($v!=='')$q->where($db->quoteName('p.'.$key).'='.$db->quote($v));}
        foreach(['status','is_primary'] as $key){$v=$this->getState('filter.'.$key); if($v!==''&&$v!==null)$q->where($db->quoteName('p.'.$key).'='.(int)$v);}
        return $q->order($db->quoteName('p.created').' DESC');
    }
}
