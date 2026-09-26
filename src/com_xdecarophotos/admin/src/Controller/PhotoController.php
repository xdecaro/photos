<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use xdecaro\Core\Integration\EntityReference;
use xdecaro\Component\Photos\Site\Service\PhotoWorkflowService;

final class PhotoController extends FormController
{
    public function upload(): void
    {
        Session::checkToken('post') or jexit('JINVALID_TOKEN');
        $user=$this->app->getIdentity(); if(!$user->authorise('core.create','com_xdecarophotos')) throw new \RuntimeException('Not authorised.',403);
        $owner=new EntityReference($this->input->getCmd('owner_component'),$this->input->getCmd('owner_entity'),$this->input->getString('owner_id'));
        $cc=$this->input->getCmd('context_component'); $ce=$this->input->getCmd('context_entity'); $ci=$this->input->getString('context_id'); $context=($cc===''&&$ce===''&&$ci==='')?null:new EntityReference($cc,$ce,$ci);
        $file=$this->input->files->get('image',null,'array')??[]; $preset=$this->input->getCmd('preset')?:null;
        $crop=[]; foreach(['crop_x','crop_y','crop_width','crop_height','rotation'] as $key)$crop[$key]=(float)$this->input->getFloat($key, $key==='crop_width'||$key==='crop_height'?1.0:0.0);
        $workflow=Factory::getContainer()->get(PhotoWorkflowService::class); $workflow->processUpload($owner,$context,$file,$this->input->getCmd('photo_type','original'),$preset,$crop,(int)$user->id,$this->input->getBool('is_primary',true));
        $this->app->enqueueMessage('Foto salvata correttamente.','message'); $this->setRedirect(Route::_('index.php?option=com_xdecarophotos&view=gallery',false));
    }
}
