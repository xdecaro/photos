<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
require dirname(__DIR__,2).'/src/com_xdecarophotos/site/src/Preset/PresetDefinition.php';
require dirname(__DIR__,2).'/src/com_xdecarophotos/site/src/Preset/PresetRegistry.php';
use xdecaro\Component\Photos\Site\Preset\PresetRegistry;
$r=new PresetRegistry();
$a=$r->get('avatar'); same(1.0,$a->aspectRatio,'avatar ratio');
$p=$r->get('passport'); ok($p->aspectRatio < 1.0,'passport vertical'); ok($p->cropDriven,'passport crop-driven');
$t=$r->get('thumbnail'); ok($t->width>0 && $t->height>0,'thumbnail dimensions');
expectException(fn()=>$r->get('unknown'),\InvalidArgumentException::class,'unknown preset');
echo "PASS PresetRegistryTest\n";
