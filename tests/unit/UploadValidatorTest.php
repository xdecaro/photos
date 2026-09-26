<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
require dirname(__DIR__, 2) . '/src/com_xdecarophotos/site/src/Value/ValidatedImage.php';
require dirname(__DIR__, 2) . '/src/com_xdecarophotos/site/src/Service/UploadValidator.php';
use xdecaro\Component\Photos\Site\Service\UploadValidator;

$dir = sys_get_temp_dir() . '/xdecarophotos-validator-' . bin2hex(random_bytes(4));
mkdir($dir);
$png = $dir . '/tiny.png';
file_put_contents($png, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2n1sAAAAASUVORK5CYII='));
$validator = new UploadValidator(1024 * 1024);
$image = $validator->validate($png, 'Luca De Caro.png', filesize($png));
same('image/png', $image->mimeType, 'PNG MIME');
same(1, $image->width, 'PNG width');
same(1, $image->height, 'PNG height');
$fake = $dir . '/evil.jpg';
file_put_contents($fake, '<?php echo "evil";');
expectException(fn() => $validator->validate($fake, 'evil.jpg', filesize($fake)), \InvalidArgumentException::class, 'fake image rejected');
expectException(fn() => (new UploadValidator(10))->validate($png, 'tiny.png', filesize($png)), \InvalidArgumentException::class, 'size limit');
@unlink($png); @unlink($fake); @rmdir($dir);
echo "PASS UploadValidatorTest\n";
