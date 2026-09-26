<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$root=dirname(__DIR__,2);
$viewFiles=[
 'src/com_xdecarophotos/admin/src/View/Photos/HtmlView.php',
 'src/com_xdecarophotos/admin/src/View/Gallery/HtmlView.php'
];
foreach($viewFiles as $file){ok(is_file($root.'/'.$file),"missing {$file}");$src=file_get_contents($root.'/'.$file);contains('AssetService',$src,'Core AssetService');contains('useComponents',$src,'Core components UI');}
foreach(['src/com_xdecarophotos/admin/tmpl/photos/default.php','src/com_xdecarophotos/admin/tmpl/gallery/default.php'] as $file){ok(is_file($root.'/'.$file),"missing {$file}");contains('xdecaro-scope',file_get_contents($root.'/'.$file),'shared UI scope');}
$css=file_get_contents($root.'/src/com_xdecarophotos/media/css/photos.css');
foreach(['.xdecaro-button{','.xdecaro-card{','.xdecaro-badge{'] as $generic) notContains($generic,str_replace(' ','',$css),'must not redefine Core primitives');
echo "PASS CoreUiContractTest\n";
