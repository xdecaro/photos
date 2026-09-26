<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
interface ImageProcessorInterface
{
    public function render(string $source,string $destination,string $mime,int $width,int $height,array $crop,int $rotation): void;
}
