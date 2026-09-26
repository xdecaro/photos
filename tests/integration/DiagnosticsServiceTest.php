<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
require dirname(__DIR__,2).'/src/com_xdecarophotos/admin/src/Service/DiagnosticsService.php';
use xdecaro\Component\Photos\Administrator\Service\DiagnosticsService;
$dir=sys_get_temp_dir().'/xdecarophotos-diag-'.bin2hex(random_bytes(4));mkdir($dir);
$service=new DiagnosticsService('0.1.0','2.1.0',$dir,['entity_reference'=>true,'asset_service'=>true],['png','jpeg'],'12M');
$r=$service->report();
same('0.1.0',$r['component_version'],'component version');same('0.1.0',$r['package_version'],'package version');same('2.1.0',$r['core_version'],'core version');same(true,$r['entity_reference'],'reference capability');same(true,$r['asset_service'],'asset capability');same(true,$r['storage_writable'],'storage writable');same(['png','jpeg'],$r['image_formats'],'formats');same('12M',$r['php_upload_limit'],'upload limit');
$text=json_encode($r);foreach(['owner_id','owner_name','photo_id','Luca'] as $private)notContains($private,$text,'diagnostics personal data');rmdir($dir);echo "PASS DiagnosticsServiceTest\n";
