<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$root=dirname(__DIR__,2);$file=$root.'/src/com_xdecarophotos/admin/tmpl/gallery/default.php';ok(is_file($file),'gallery template missing');$html=file_get_contents($file);
contains('xdecarophotos-gallery',$html,'gallery grid hook');contains('aria-label',$html,'accessible actions');contains('alt=', $html,'image alt');notContains('<table',$html,'gallery mobile must not require table');contains('Modifica',$html,'text action label');
echo "PASS GalleryMarkupTest\n";
