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
        $assets=new AssetService(); $assets->useComponents($this->getDocument()->getWebAssetManager());
        $this->getDocument()->getWebAssetManager()->useStyle('com_xdecarophotos.photos');
        $this->items=$this->get('Items')?:[]; parent::display($tpl);
    }
}
