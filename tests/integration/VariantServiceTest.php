<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$base=dirname(__DIR__,2).'/src/com_xdecarophotos/site/src/';
foreach(['Preset/PresetDefinition.php','Preset/PresetRegistry.php','Value/PhotoVariant.php','Service/ImageProcessorInterface.php','Repository/VariantRepositoryInterface.php','Service/VariantService.php'] as $f) require $base.$f;
use xdecaro\Component\Photos\Site\Service\ImageProcessorInterface; use xdecaro\Component\Photos\Site\Service\VariantService; use xdecaro\Component\Photos\Site\Repository\VariantRepositoryInterface; use xdecaro\Component\Photos\Site\Value\PhotoVariant; use xdecaro\Component\Photos\Site\Preset\PresetRegistry;
final class FakeProcessor implements ImageProcessorInterface { public function render(string $source,string $destination,string $mime,int $width,int $height,array $crop,int $rotation):void { file_put_contents($destination,'variant:'.file_get_contents($source)); } }
final class FakeVariants implements VariantRepositoryInterface { public array $rows=[]; public function insert(PhotoVariant $v):PhotoVariant{$this->rows[$v->id]=$v;return $v;} public function nextId():int{return count($this->rows)+1;} public function byPhoto(int $id):array{return array_values(array_filter($this->rows,fn($v)=>$v->photoId===$id));} public function delete(int $id):void{unset($this->rows[$id]);} }
$dir=sys_get_temp_dir().'/xdecaro-var-'.bin2hex(random_bytes(4)); mkdir($dir); $src=$dir.'/original.png'; file_put_contents($src,'ORIGINAL'); $before=hash_file('sha256',$src); $repo=new FakeVariants(); $service=new VariantService(new PresetRegistry(),$repo,new FakeProcessor(),$dir.'/variants');
$v=$service->createVariantFromPath(7,$src,'image/png','avatar',['crop_x'=>0,'crop_y'=>0,'crop_width'=>1,'crop_height'=>1,'rotation'=>90]);
same(7,$v->photoId,'photo id'); same('avatar',$v->preset,'preset'); same(90,$v->rotation,'rotation'); same($before,hash_file('sha256',$src),'original unchanged'); ok(is_file($v->path),'variant exists');
array_map('unlink',glob($dir.'/variants/*')?:[]); @rmdir($dir.'/variants'); unlink($src); rmdir($dir); echo "PASS VariantServiceTest\n";
