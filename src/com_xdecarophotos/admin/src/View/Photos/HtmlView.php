<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\View\Photos;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use xdecaro\Core\Asset\AssetService;

final class HtmlView extends BaseHtmlView
{
    public array $items=[];
    public $pagination;
    public function display($tpl=null): void
    {
        $assets=new AssetService();
        $assets->useComponents($this->getDocument()->getWebAssetManager());
        $this->getDocument()->getWebAssetManager()->useStyle('com_xdecarophotos.photos')->useScript('com_xdecarophotos.gallery');
        $this->items=$this->getModel()->getItems();
        $this->pagination=$this->getModel()->getPagination();
        ToolbarHelper::title('xdecaro Photos','images');
        ToolbarHelper::custom('photo.add','new','new','Nuova foto',false);
        ToolbarHelper::preferences('com_xdecarophotos');
        parent::display($tpl);
    }
}
