<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\View\Diagnostics;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use xdecaro\Component\Photos\Administrator\Service\DiagnosticsService;
use xdecaro\Core\Asset\AssetService;

final class HtmlView extends BaseHtmlView
{
    public array $report = [];
    public function display($tpl=null): void
    {
        (new AssetService())->useComponents($this->getDocument()->getWebAssetManager());
        $path = JPATH_ROOT . '/images/xdecaro/photos';
        if (!is_dir($path)) @mkdir($path,0755,true);
        $this->report = DiagnosticsService::fromRuntime($path)->report();
        ToolbarHelper::title('xdecaro Photos — Diagnostica','health');
        parent::display($tpl);
    }
}
