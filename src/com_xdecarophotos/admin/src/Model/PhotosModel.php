<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

final class PhotosModel extends ListModel
{
    protected $filter_fields = ['id','owner_component','owner_entity','owner_id','photo_type','status','season','is_primary','created'];

    protected function getListQuery(): QueryInterface
    {
        $db=$this->getDatabase();
        $q=$db->getQuery(true)->select('p.*')->from($db->quoteName('#__xdecarophotos_photos','p'));
        foreach ([
            'owner_component'=>'owner_component','owner_entity'=>'owner_entity','owner_id'=>'owner_id','photo_type'=>'photo_type','season'=>'season'
        ] as $state=>$column) {
            $value=trim((string)$this->getState('filter.'.$state,''));
            if($value!=='') $q->where($db->quoteName('p.'.$column).' = '.$db->quote($value));
        }
        $status=$this->getState('filter.status',''); if($status!=='') $q->where($db->quoteName('p.status').' = '.(int)$status);
        $primary=$this->getState('filter.is_primary',''); if($primary!=='') $q->where($db->quoteName('p.is_primary').' = '.(int)$primary);
        $q->order($db->quoteName('p.created').' DESC');
        return $q;
    }

    protected function populateState($ordering = 'p.created', $direction = 'DESC'): void
    {
        $app=$this->getApplication();
        foreach(['owner_component','owner_entity','owner_id','photo_type','season','status','is_primary'] as $name){$this->setState('filter.'.$name,$app->getUserStateFromRequest($this->context.'.filter.'.$name,'filter_'.$name,''));}
        parent::populateState($ordering,$direction);
    }
}
