<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;

use RuntimeException;

final class GdImageProcessor implements ImageProcessorInterface
{
    public function render(string $source,string $destination,string $mime,int $width,int $height,array $crop,int $rotation): void
    {
        if (!extension_loaded('gd')) throw new RuntimeException('GD extension is required for server-side variants.');
        $bytes=@file_get_contents($source); $image=$bytes!==false ? @imagecreatefromstring($bytes) : false;
        if ($image===false) throw new RuntimeException('Unable to decode source image.');
        $sw=imagesx($image); $sh=imagesy($image);
        $cx=max(0.0,min(1.0,(float)($crop['crop_x']??0))); $cy=max(0.0,min(1.0,(float)($crop['crop_y']??0)));
        $cw=max(0.0001,min(1.0,(float)($crop['crop_width']??1))); $ch=max(0.0001,min(1.0,(float)($crop['crop_height']??1)));
        $sx=(int)round($cx*$sw); $sy=(int)round($cy*$sh); $srcW=max(1,min($sw-$sx,(int)round($cw*$sw))); $srcH=max(1,min($sh-$sy,(int)round($ch*$sh)));
        $canvas=imagecreatetruecolor($width,$height);
        if ($mime==='image/png' || $mime==='image/webp') { imagealphablending($canvas,false); imagesavealpha($canvas,true); $transparent=imagecolorallocatealpha($canvas,0,0,0,127); imagefill($canvas,0,0,$transparent); }
        imagecopyresampled($canvas,$image,0,0,$sx,$sy,$width,$height,$srcW,$srcH);
        $rotation=((int)$rotation%360+360)%360;
        if (in_array($rotation,[90,180,270],true)) { $rotated=imagerotate($canvas,360-$rotation,0); if($rotated!==false){imagedestroy($canvas);$canvas=$rotated;} }
        $ok=match($mime){'image/jpeg'=>imagejpeg($canvas,$destination,90),'image/png'=>imagepng($canvas,$destination,6),'image/webp'=>function_exists('imagewebp')?imagewebp($canvas,$destination,90):false,default=>false};
        imagedestroy($canvas); imagedestroy($image);
        if(!$ok) throw new RuntimeException('Unable to encode generated variant.');
    }
}
