<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Value;
use xdecaro\Core\Integration\EntityReference;
final class PhotoRecord
{
    public function __construct(
        public readonly int $id,
        public readonly string $ownerComponent,
        public readonly string $ownerEntity,
        public readonly string $ownerId,
        public readonly ?string $contextComponent,
        public readonly ?string $contextEntity,
        public readonly ?string $contextId,
        public readonly string $photoType,
        public readonly string $originalPath,
        public readonly string $mimeType,
        public readonly bool $isPrimary=false,
        public readonly int $status=1,
        public readonly ?string $season=null
    ) {}
    public static function fromPayload(int $id, EntityReference $owner, array $payload): self
    {
        $ctx=$payload['context']??null;
        return new self($id,$owner->component,$owner->entity,(string)$owner->id,$ctx?->component,$ctx?->entity,$ctx?(string)$ctx->id:null,(string)($payload['photo_type']??'original'),(string)($payload['original_path']??''),(string)($payload['mime_type']??''),(bool)($payload['is_primary']??false),(int)($payload['status']??1),isset($payload['season'])?(string)$payload['season']:null);
    }
    public function samePrimaryGroup(self $other): bool
    {
        return $this->ownerComponent===$other->ownerComponent && $this->ownerEntity===$other->ownerEntity && $this->ownerId===$other->ownerId && $this->photoType===$other->photoType && $this->contextComponent===$other->contextComponent && $this->contextEntity===$other->contextEntity && $this->contextId===$other->contextId;
    }
    public function withPrimary(bool $primary): self
    {
        return new self($this->id,$this->ownerComponent,$this->ownerEntity,$this->ownerId,$this->contextComponent,$this->contextEntity,$this->contextId,$this->photoType,$this->originalPath,$this->mimeType,$primary,$this->status,$this->season);
    }
}
