<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\View\Diagnostics;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use xdecaro\Core\Asset\AssetService;
use xdecaro\Component\Photos\Administrator\Service\DiagnosticsService;
final class HtmlView extends BaseHtmlView
{
    public array $diagnostics=[];
    public function display($tpl=null):void { $wam=$this->getDocument()->getWebAssetManager();(new AssetService())->useComponents($wam);$this->diagnostics=Factory::getContainer()->get(DiagnosticsService::class)->collect();parent::display($tpl); }
}
