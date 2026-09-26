<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use InvalidArgumentException;

final class ValidatedImage
{
    public function __construct(public readonly string $tmpPath, public readonly string $mimeType, public readonly string $extension, public readonly int $size, public readonly int $width, public readonly int $height) {}
}

final class UploadValidator
{
    private const MIME_EXT = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    public function __construct(private readonly int $maxBytes = 10485760) {}
    public function validate(string $tmpPath, string $originalName, int $size): ValidatedImage
    {
        if ($size <= 0 || $size > $this->maxBytes) throw new InvalidArgumentException('Invalid image size.');
        if (!is_file($tmpPath) || !is_readable($tmpPath)) throw new InvalidArgumentException('Upload is not readable.');
        $info = @getimagesize($tmpPath); if ($info === false) throw new InvalidArgumentException('File is not a decodable image.');
        $finfo = new \finfo(FILEINFO_MIME_TYPE); $mime = (string)$finfo->file($tmpPath);
        if (!isset(self::MIME_EXT[$mime])) throw new InvalidArgumentException('Unsupported image MIME type.');
        $detectedMime = $info['mime'] ?? ''; if ($detectedMime !== $mime) throw new InvalidArgumentException('Image MIME mismatch.');
        $originalExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExts = $mime === 'image/jpeg' ? ['jpg','jpeg'] : [self::MIME_EXT[$mime]];
        if (!in_array($originalExt, $allowedExts, true)) throw new InvalidArgumentException('File extension does not match image content.');
        return new ValidatedImage($tmpPath,$mime,self::MIME_EXT[$mime],$size,(int)$info[0],(int)$info[1]);
    }
}
