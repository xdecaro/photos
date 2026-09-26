<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$base=dirname(__DIR__,2).'/src/com_xdecarophotos/site/src/';
foreach(['Value/PhotoVariant.php','Service/PhotoDeletionService.php'] as $f) require $base.$f;
use xdecaro\Component\Photos\Site\Service\PhotoDeletionService; use xdecaro\Component\Photos\Site\Value\PhotoVariant;
final class FakeDeletePhotos { public array $calls=[]; public function deactivatePhoto(int $id):void{$this->calls[]=['deactivate',$id];} public function deletePhoto(int $id):void{$this->calls[]=['delete',$id];} }
final class FakeDeleteVariants { public array $rows; public function __construct(array $rows){$this->rows=$rows;} public function byPhoto(int $id):array{return array_values(array_filter($this->rows,fn($v)=>$v->photoId===$id));} public function delete(int $id):void{$this->rows=array_values(array_filter($this->rows,fn($v)=>$v->id!==$id));} }
$dir=sys_get_temp_dir().'/xdecaro-del-'.bin2hex(random_bytes(4)); mkdir($dir); $orig=$dir.'/original'; $var=$dir.'/variant'; file_put_contents($orig,'o');file_put_contents($var,'v');
$v=new PhotoVariant(1,9,'avatar',$var,'image/png',100,100,0,0,1,1,0,'x'); $p=new FakeDeletePhotos();$r=new FakeDeleteVariants([$v]);$svc=new PhotoDeletionService($p,$r);
$svc->deactivate(9); ok(is_file($orig)&&is_file($var),'deactivate keeps files');
$svc->deleteVariant(1,$var); ok(is_file($orig)&&!is_file($var),'variant delete keeps original');
file_put_contents($var,'v'); $r->rows=[$v]; $svc->deletePhoto(9,$orig); ok(!is_file($orig)&&!is_file($var),'photo deletion removes own files'); same([['deactivate',9],['delete',9]],$p->calls,'no external owner mutation'); @rmdir($dir); echo "PASS DeletionIntegrityTest\n";
