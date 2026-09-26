<?php

declare(strict_types=1);

function fail(string $message): never {
    fwrite(STDERR, "FAIL: {$message}\n");
    exit(1);
}

$root = dirname(__DIR__, 2);
$versionFile = $root . '/VERSION';
$componentManifest = $root . '/src/com_xdecarophotos/admin/xdecarophotos.xml';
$packageManifest = $root . '/package/pkg_xdecarophotos/pkg_xdecarophotos.xml';
$installer = $root . '/package/pkg_xdecarophotos/script.php';

foreach ([$versionFile, $componentManifest, $packageManifest, $installer] as $file) {
    if (!is_file($file)) {
        fail("missing {$file}");
    }
}

$version = trim((string) file_get_contents($versionFile));
if ($version !== '0.1.0') {
    fail('VERSION must be 0.1.0');
}

$component = (string) file_get_contents($componentManifest);
$package = (string) file_get_contents($packageManifest);
$script = (string) file_get_contents($installer);

$checks = [
    [$component, '<name>com_xdecarophotos</name>', 'component identifier'],
    [$component, 'xdecaro\\Component\\Photos', 'component namespace'],
    [$component, '<version>0.1.0</version>', 'component version'],
    [$package, '<name>pkg_xdecarophotos</name>', 'package identifier'],
    [$package, '<version>0.1.0</version>', 'package version'],
    [$script, "'6.1.3'", 'Joomla minimum'],
    [$script, "'2.1.0'", 'Core minimum'],
];

foreach ($checks as [$haystack, $needle, $label]) {
    if (!str_contains($haystack, $needle)) {
        fail("missing {$label}");
    }
}

echo "PASS manifest contract\n";
