<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\View\Gallery;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use xdecaro\Core\Asset\AssetService;
final class HtmlView extends BaseHtmlView
{
    public array $items=[];
    public function display($tpl=null): void
    {
        $wam=$this->getDocument()->getWebAssetManager(); (new AssetService())->useComponents($wam); $wam->getRegistry()->addExtensionRegistryFile('com_xdecarophotos'); $wam->useStyle('com_xdecarophotos.photos')->useScript('com_xdecarophotos.gallery');
        $this->items=$this->get('Items')?:[];
        foreach($this->items as $item){$item->preview_url=Uri::root(true).'/images/xdecaro/photos/'.ltrim((string)$item->original_path,'/');$item->edit_url=Route::_('index.php?option=com_xdecarophotos&view=studio&owner_component='.rawurlencode((string)$item->owner_component).'&owner_entity='.rawurlencode((string)$item->owner_entity).'&owner_id='.rawurlencode((string)$item->owner_id),false);}
        parent::display($tpl);
    }
}
