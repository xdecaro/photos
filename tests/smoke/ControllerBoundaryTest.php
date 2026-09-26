<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$root=dirname(__DIR__,2);$file=$root.'/src/com_xdecarophotos/admin/src/Controller/PhotoController.php';$src=file_get_contents($file);
foreach(['function upload','Session::checkToken','assertWriteAccess(\'core.create\'','PhotoReference::fromParts','DatabasePhotoRepository','function saveVariant','VariantService','function setPrimary','function file'] as $needle) contains($needle,$src,'controller boundary');
$studio=file_get_contents($root.'/src/com_xdecarophotos/admin/tmpl/studio/default.php');contains('enctype="multipart/form-data"',$studio,'initial upload form');contains('name="owner_component"',$studio,'owner component input');contains('Carica originale e continua',$studio,'upload submit');
echo "PASS ControllerBoundaryTest\n";
