<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use RuntimeException;
use xdecaro\Component\Photos\Site\Preset\PresetRegistry;
use xdecaro\Component\Photos\Site\Repository\VariantRepositoryInterface;
use xdecaro\Component\Photos\Site\Value\PhotoVariant;

final class VariantService
{
    public function __construct(private readonly PresetRegistry $presets,private readonly VariantRepositoryInterface $repository,private readonly ImageProcessorInterface $processor,private readonly string $variantDirectory) {}
    public function createVariantFromPath(int $photoId,string $source,string $mime,string $preset,array $options=[]): PhotoVariant
    {
        $definition=$this->presets->get($preset);
        if(!is_file($source)) throw new RuntimeException('Source photo not found.');
        if(!is_dir($this->variantDirectory) && !mkdir($this->variantDirectory,0755,true) && !is_dir($this->variantDirectory)) throw new RuntimeException('Unable to create variant directory.');
        $ext=match($mime){'image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp',default=>throw new RuntimeException('Unsupported variant MIME type.')};
        $path=rtrim($this->variantDirectory,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.bin2hex(random_bytes(20)).'.'.$ext;
        $crop=['crop_x'=>(float)($options['crop_x']??0),'crop_y'=>(float)($options['crop_y']??0),'crop_width'=>(float)($options['crop_width']??1),'crop_height'=>(float)($options['crop_height']??1)];
        $rotation=(int)($options['rotation']??0);
        $this->processor->render($source,$path,$mime,$definition->width,$definition->height,$crop,$rotation);
        if(!is_file($path)) throw new RuntimeException('Variant processor did not create output.');
        $variant=new PhotoVariant($this->repository->nextId(),$photoId,$preset,$path,$mime,$definition->width,$definition->height,$crop['crop_x'],$crop['crop_y'],$crop['crop_width'],$crop['crop_height'],$rotation,hash_file('sha256',$path)?:'');
        return $this->repository->insert($variant);
    }
}
