<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Service;
use InvalidArgumentException;
use xdecaro\Core\Integration\EntityReference;
final class PhotoWorkflowService
{
    public function __construct(private readonly UploadValidator $validator,private readonly PhotoStorage $storage,private readonly PhotoService $photos,private readonly VariantService $variants) {}
    public function processUpload(EntityReference $owner,?EntityReference $context,array $file,string $photoType='original',?string $preset=null,array $crop=[],int $userId=0,bool $primary=false): array
    {
        if((int)($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK) throw new InvalidArgumentException('Upload failed.');
        $image=$this->validator->validate((string)($file['tmp_name']??''),(string)($file['name']??''),(int)($file['size']??0));
        $stored=$this->storage->storeOriginal($image);
        try {
            $photo=$this->photos->createPhoto($owner,['context'=>$context,'photo_type'=>$photoType,'original_path'=>$stored->relativePath,'mime_type'=>$image->mimeType,'width'=>$image->width,'height'=>$image->height,'file_size'=>$image->size,'created_by'=>$userId]);
            $variant=null; if($preset!==null&&$preset!=='')$variant=$this->variants->createVariantFromPath($photo->id,$stored->absolutePath,$preset,$crop);
            if($primary)$this->photos->setPrimaryPhoto($photo->id);
            return ['photo'=>$this->photos->getPhoto($photo->id)??$photo,'variant'=>$variant];
        } catch(\Throwable $e) { $this->storage->deleteOriginal($stored->relativePath); throw $e; }
    }
}
