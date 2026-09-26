<?php
declare(strict_types=1);
namespace xdecaro\Core\Integration { final class EntityReference { public function __construct(private string $c,private string $e,private string $i){} public function getComponent():string{return $this->c;} public function getEntity():string{return $this->e;} public function getId():string{return $this->i;} } }
namespace {
require dirname(__DIR__).'/bootstrap.php';
require dirname(__DIR__,2).'/src/com_xdecarophotos/site/src/Value/PhotoRecord.php';
use xdecaro\Core\Integration\EntityReference; use xdecaro\Component\Photos\Site\Value\PhotoRecord;
$owner=new EntityReference('com_xdecaropeople','person','125');
$a=PhotoRecord::fromPayload(1,$owner,['photo_type'=>'avatar','original_path'=>'a','mime_type'=>'image/png','width'=>1,'height'=>1,'file_size'=>1,'is_primary'=>1]);
$b=PhotoRecord::fromPayload(2,$owner,['photo_type'=>'avatar','original_path'=>'b','mime_type'=>'image/png','width'=>1,'height'=>1,'file_size'=>1]);
ok($a->samePrimaryGroup($b),'same group');
$c=PhotoRecord::fromPayload(3,$owner,['photo_type'=>'passport','original_path'=>'c','mime_type'=>'image/png','width'=>1,'height'=>1,'file_size'=>1]);
ok(!$a->samePrimaryGroup($c),'different type group');
$context=['component'=>'com_xdecarocompetitions','entity'=>'competition','id'=>'32'];
$d=PhotoRecord::fromPayload(4,$owner,['photo_type'=>'avatar','context'=>$context,'original_path'=>'d','mime_type'=>'image/png','width'=>1,'height'=>1,'file_size'=>1]);
ok(!$a->samePrimaryGroup($d),'null context separate group');
echo "PASS PrimaryPhotoTest\n";
}
