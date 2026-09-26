<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require dirname(__DIR__, 2) . '/src/com_xdecarophotos/site/src/Value/ValidatedImage.php';
require dirname(__DIR__, 2) . '/src/com_xdecarophotos/site/src/Value/StoredPhoto.php';
require dirname(__DIR__, 2) . '/src/com_xdecarophotos/site/src/Service/PhotoStorage.php';
use xdecaro\Component\Photos\Site\Service\PhotoStorage;
use xdecaro\Component\Photos\Site\Value\ValidatedImage;

$base = sys_get_temp_dir() . '/xdecarophotos-storage-' . bin2hex(random_bytes(4));
$tmp = tempnam(sys_get_temp_dir(), 'photo');
file_put_contents($tmp, 'image-bytes');
$image = new ValidatedImage($tmp, 'Luca De Caro.png', 'image/png', 1, 1, filesize($tmp), 1);
$storage = new PhotoStorage($base);
$stored = $storage->storeOriginal($image);
ok(is_file($stored->absolutePath), 'stored original exists');
notContains('Luca', $stored->relativePath, 'personal filename leaked');
notContains('..', $stored->relativePath, 'traversal path');
contains('originals/', $stored->relativePath, 'original folder');
$storage->deleteOriginal($stored->relativePath);
ok(!is_file($stored->absolutePath), 'original removed');
@unlink($tmp);
@rmdir($base . '/originals'); @rmdir($base . '/variants'); @rmdir($base);
echo "PASS PhotoStorageTest\n";
