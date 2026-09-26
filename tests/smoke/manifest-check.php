<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
$root = dirname(__DIR__, 2);
$required = [
    'VERSION',
    'src/com_xdecarophotos/admin/xdecarophotos.xml',
    'package/pkg_xdecarophotos/pkg_xdecarophotos.xml',
    'package/pkg_xdecarophotos/script.php',
];
foreach ($required as $file) {
    ok(is_file($root . '/' . $file), "missing {$file}");
}
same('0.1.1', trim(file_get_contents($root . '/VERSION')), 'VERSION mismatch');
$component = file_get_contents($root . '/src/com_xdecarophotos/admin/xdecarophotos.xml');
$package = file_get_contents($root . '/package/pkg_xdecarophotos/pkg_xdecarophotos.xml');
$script = file_get_contents($root . '/package/pkg_xdecarophotos/script.php');
contains('<name>com_xdecarophotos</name>', $component, 'component id');
contains('prefix="xdecaro\\Component\\Photos"', $component, 'namespace prefix');
contains('<version>0.1.1</version>', $component, 'component version');
contains('<name>pkg_xdecarophotos</name>', $package, 'package id');
contains('<version>0.1.1</version>', $package, 'package version');
contains("MIN_JOOMLA = '6.1.3'", $script, 'Joomla minimum');
contains("MIN_CORE = '2.1.0'", $script, 'Core minimum');
contains("pkg_core", $script, 'Core 2.1.0 package element');
echo "PASS manifest-check\n";
