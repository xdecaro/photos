<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\View\Photos;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use xdecaro\Core\Asset\AssetService;
final class HtmlView extends BaseHtmlView
{
    public array $items=[];
    public function display($tpl=null): void
    {
        $wam=$this->getDocument()->getWebAssetManager(); (new AssetService())->useComponents($wam); $wam->getRegistry()->addExtensionRegistryFile('com_xdecarophotos'); $wam->useStyle('com_xdecarophotos.photos');
        $this->items=$this->get('Items')?:[]; parent::display($tpl);
    }
}
