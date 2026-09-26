<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$root=dirname(__DIR__,2);$file=$root.'/src/com_xdecarophotos/admin/tmpl/studio/default.php';ok(is_file($file),'studio template missing');$html=file_get_contents($file);
contains('type="file"',$html,'file upload always present');contains('accept="image/jpeg,image/png,image/webp"',$html,'image accept');contains('type="range"',$html,'zoom control');contains('Ruota a sinistra',$html,'rotation text control');contains('Sposta a sinistra',$html,'keyboard/pointer independent crop control');contains('Salva variante',$html,'save action');contains('aria-live',$html,'camera status accessibility');
$css=file_get_contents($root.'/src/com_xdecarophotos/media/css/photos.css');contains('prefers-reduced-motion',$css,'reduced motion');echo "PASS PhotoStudioMarkupTest\n";
