<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\View\Studio;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use xdecaro\Core\Asset\AssetService;
final class HtmlView extends BaseHtmlView
{
    public string $ownerComponent='com_xdecaropeople'; public string $ownerEntity='person'; public string $ownerId='';
    public function display($tpl=null): void
    {
        $input=Factory::getApplication()->getInput(); $this->ownerComponent=$input->getCmd('owner_component','com_xdecaropeople'); $this->ownerEntity=$input->getCmd('owner_entity','person'); $this->ownerId=$input->getString('owner_id','');
        $wam=$this->getDocument()->getWebAssetManager(); (new AssetService())->useComponents($wam); $wam->getRegistry()->addExtensionRegistryFile('com_xdecarophotos'); $wam->useStyle('com_xdecarophotos.photos')->useScript('com_xdecarophotos.studio'); parent::display($tpl);
    }
}
