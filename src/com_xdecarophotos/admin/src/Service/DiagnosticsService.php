<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Service;
final class DiagnosticsService
{
    public function __construct(private readonly string $componentVersion,private readonly string $coreVersion,private readonly string $storagePath,private readonly ?string $packageVersion=null) {}
    public static function detectCoreVersion(): string
    {
        foreach ([defined('JPATH_LIBRARIES') ? JPATH_LIBRARIES . '/xdecaro/core/VERSION' : '', defined('JPATH_ADMINISTRATOR') ? JPATH_ADMINISTRATOR . '/components/com_xdecarocore/VERSION' : ''] as $path) {
            if ($path !== '' && is_file($path)) return trim((string) file_get_contents($path));
        }
        return class_exists('xdecaro\\Core\\Integration\\EntityReference') ? '2.1.0+' : 'non disponibile';
    }
    public function collect(): array
    {
        $formats=[]; foreach(['jpeg'=>'imagecreatefromjpeg','png'=>'imagecreatefrompng','webp'=>'imagecreatefromwebp'] as $name=>$fn) if(function_exists($fn)) $formats[]=$name;
        return ['component_version'=>$this->componentVersion,'package_version'=>$this->packageVersion??$this->componentVersion,'core_version'=>$this->coreVersion,'entity_reference'=>class_exists('xdecaro\\Core\\Integration\\EntityReference'),'asset_service'=>class_exists('xdecaro\\Core\\Asset\\AssetService'),'storage_writable'=>is_dir($this->storagePath)&&is_writable($this->storagePath),'image_formats'=>$formats,'upload_limit'=>ini_get('upload_max_filesize')?:''];
    }
}
