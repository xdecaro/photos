<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\View\Studio;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use xdecaro\Core\Asset\AssetService;

final class HtmlView extends BaseHtmlView
{
    public int $photoId = 0;

    public function display($tpl = null): void
    {
        $assets = new AssetService();
        $assets->useComponents($this->getDocument()->getWebAssetManager());
        $this->getDocument()->getWebAssetManager()
            ->useStyle('com_xdecarophotos.photos')
            ->useScript('com_xdecarophotos.photo-studio');
        $this->photoId = Factory::getApplication()->input->getInt('id', 0);
        ToolbarHelper::title('xdecaro Photos — Photo Studio', 'camera');
        parent::display($tpl);
    }
}
