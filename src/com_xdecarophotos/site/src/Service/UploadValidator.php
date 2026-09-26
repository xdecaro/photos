<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use finfo;
use InvalidArgumentException;
use xdecaro\Component\Photos\Site\Value\ValidatedImage;

final class UploadValidator
{
    private const MIME_EXTENSIONS = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
    ];

    public function __construct(private readonly int $maxBytes = 12582912) {}

    public function validate(string $tmpPath, string $originalName, int $size): ValidatedImage
    {
        if ($size <= 0 || $size > $this->maxBytes) {
            throw new InvalidArgumentException('Image size is outside the allowed limit.');
        }
        if (!is_file($tmpPath) || !is_readable($tmpPath)) {
            throw new InvalidArgumentException('Uploaded image is not readable.');
        }

        $info = @getimagesize($tmpPath);
        if ($info === false || empty($info[0]) || empty($info[1])) {
            throw new InvalidArgumentException('Uploaded content is not a decodable image.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath) ?: '';
        $decodedMime = isset($info['mime']) ? strtolower((string) $info['mime']) : '';
        if ($mime !== $decodedMime || !isset(self::MIME_EXTENSIONS[$mime])) {
            throw new InvalidArgumentException('Unsupported or inconsistent image MIME type.');
        }

        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension === '' || !in_array($extension, self::MIME_EXTENSIONS[$mime], true)) {
            throw new InvalidArgumentException('Image extension does not match decoded content.');
        }

        return new ValidatedImage(
            $tmpPath,
            basename($originalName),
            $mime,
            (int) $info[0],
            (int) $info[1],
            $size,
            $this->readOrientation($tmpPath, $mime),
        );
    }

    private function readOrientation(string $path, string $mime): int
    {
        if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
            return 1;
        }
        $exif = @exif_read_data($path);
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;
        return ($orientation >= 1 && $orientation <= 8) ? $orientation : 1;
    }
}
