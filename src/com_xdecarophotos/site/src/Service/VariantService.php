<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
use RuntimeException;
use xdecaro\Component\Photos\Site\Preset\PresetRegistry;
use xdecaro\Component\Photos\Site\Value\PhotoVariant;
interface ImageProcessor { public function render(string $source,string $dest,float $ratio,array $options): array; }
final class GdImageProcessor implements ImageProcessor
{
    public function render(string $source,string $dest,float $ratio,array $options): array
    {
        if(!extension_loaded('gd')) throw new RuntimeException('GD extension is required to render photo variants.');
        $info=@getimagesize($source); if(!$info) throw new RuntimeException('Invalid source image.');
        [$sw,$sh]=$info; $mime=$info['mime']??''; $create=match($mime){'image/jpeg'=>'imagecreatefromjpeg','image/png'=>'imagecreatefrompng','image/webp'=>'imagecreatefromwebp',default=>null}; if(!$create||!function_exists($create)) throw new RuntimeException('Unsupported source format.');
        $src=$create($source); if(!$src) throw new RuntimeException('Cannot decode source image.');
        $cw=(int)round(($options['crop_width']??1)*$sw); $ch=(int)round(($options['crop_height']??1)*$sh); $cx=(int)round(($options['crop_x']??0)*$sw); $cy=(int)round(($options['crop_y']??0)*$sh); $cw=max(1,min($cw,$sw-$cx));$ch=max(1,min($ch,$sh-$cy));
        if($ratio>0){ if($cw/$ch>$ratio)$cw=(int)round($ch*$ratio); else $ch=(int)round($cw/$ratio); }
        $tw=(int)($options['target_width']??max(1,$cw)); $th=(int)round($tw/$ratio); $out=imagecreatetruecolor($tw,$th); imagealphablending($out,false); imagesavealpha($out,true); imagecopyresampled($out,$src,0,0,$cx,$cy,$tw,$th,$cw,$ch);
        $rotation=((int)($options['rotation']??0))%360; if($rotation!==0){$rot=imagerotate($out,360-$rotation,0); if($rot){imagedestroy($out);$out=$rot;}}
        $ext=strtolower(pathinfo($dest,PATHINFO_EXTENSION)); $ok=match($ext){'png'=>imagepng($out,$dest,9),'webp'=>imagewebp($out,$dest,90),default=>imagejpeg($out,$dest,90)}; imagedestroy($src);imagedestroy($out); if(!$ok) throw new RuntimeException('Cannot write variant.'); $v=@getimagesize($dest); return ['width'=>(int)$v[0],'height'=>(int)$v[1],'mime'=>(string)($v['mime']??'image/jpeg')];
    }
}
final class VariantService
{
    public function __construct(private readonly PresetRegistry $presets,private readonly ImageProcessor $processor,private readonly string $variantBase,private readonly ?VariantRepository $repository=null) {}
    public function createVariantFromPath(int $photoId,string $source,string $preset,array $options=[]): PhotoVariant
    {
        $def=$this->presets->get($preset); $dir=rtrim($this->variantBase,'/\\'); if(!is_dir($dir)&&!mkdir($dir,0755,true)&&!is_dir($dir))throw new RuntimeException('Cannot create variant storage.'); $ext=$def->preserveAlpha?'png':'jpg'; $name=$photoId.'-'.bin2hex(random_bytes(12)).'.'.$ext; $dest=$dir.DIRECTORY_SEPARATOR.$name; $meta=$this->processor->render($source,$dest,$def->aspectRatio,$options+['target_width'=>$def->width]); $relative='variants/'.$name; $publicBase=dirname($dir); if(str_ends_with(str_replace('\\','/',$dir),'/variants'))$absoluteForRelative=$publicBase.DIRECTORY_SEPARATOR.$relative; else $absoluteForRelative=$dest; if($absoluteForRelative!==$dest && !is_file($absoluteForRelative)){$relative=$name;}
        $variant=new PhotoVariant($photoId,$preset,$relative,(string)$meta['mime'],(int)$meta['width'],(int)$meta['height'],isset($options['crop_x'])?(float)$options['crop_x']:null,isset($options['crop_y'])?(float)$options['crop_y']:null,isset($options['crop_width'])?(float)$options['crop_width']:null,isset($options['crop_height'])?(float)$options['crop_height']:null,(int)($options['rotation']??0),hash_file('sha256',$dest)); return $this->repository?->save($variant) ?? $variant;
    }
}
