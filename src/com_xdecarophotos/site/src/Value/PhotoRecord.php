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
        public readonly int $width,
        public readonly int $height,
        public readonly int $fileSize,
        public readonly bool $isPrimary = false,
        public readonly int $status = 1,
        public readonly ?string $season = null,
        public readonly string $title = '',
        public readonly string $description = '',
    ) {}

    public static function fromPayload(int $id, EntityReference $owner, array $payload): self
    {
        $context = isset($payload['context']) && is_array($payload['context']) ? $payload['context'] : null;
        return new self(
            $id,
            $owner->getComponent(),
            $owner->getEntity(),
            $owner->getId(),
            $context['component'] ?? null,
            $context['entity'] ?? null,
            isset($context['id']) ? (string) $context['id'] : null,
            (string) ($payload['photo_type'] ?? 'original'),
            (string) ($payload['original_path'] ?? ''),
            (string) ($payload['mime_type'] ?? ''),
            (int) ($payload['width'] ?? 0),
            (int) ($payload['height'] ?? 0),
            (int) ($payload['file_size'] ?? 0),
            (bool) ($payload['is_primary'] ?? false),
            (int) ($payload['status'] ?? 1),
            isset($payload['season']) && $payload['season'] !== '' ? (string) $payload['season'] : null,
            (string) ($payload['title'] ?? ''),
            (string) ($payload['description'] ?? ''),
        );
    }

    public static function fromRow(object $row): self
    {
        return new self(
            (int) $row->id, (string) $row->owner_component, (string) $row->owner_entity, (string) $row->owner_id,
            $row->context_component !== null ? (string) $row->context_component : null,
            $row->context_entity !== null ? (string) $row->context_entity : null,
            $row->context_id !== null ? (string) $row->context_id : null,
            (string) $row->photo_type, (string) $row->original_path, (string) $row->mime_type,
            (int) $row->width, (int) $row->height, (int) $row->file_size, (bool) $row->is_primary,
            (int) $row->status, $row->season !== null ? (string) $row->season : null,
            (string) ($row->title ?? ''), (string) ($row->description ?? ''),
        );
    }

    public function samePrimaryGroup(self $other): bool
    {
        return $this->ownerComponent === $other->ownerComponent
            && $this->ownerEntity === $other->ownerEntity
            && $this->ownerId === $other->ownerId
            && $this->photoType === $other->photoType
            && $this->contextComponent === $other->contextComponent
            && $this->contextEntity === $other->contextEntity
            && $this->contextId === $other->contextId;
    }

    public function withPrimary(bool $primary): self
    {
        return new self(...array_values(array_merge($this->asConstructorArray(), ['isPrimary' => $primary])));
    }

    public function withStatus(int $status): self
    {
        $data = $this->asConstructorArray(); $data['status'] = $status;
        return new self(...array_values($data));
    }

    private function asConstructorArray(): array
    {
        return [
            'id'=>$this->id,'ownerComponent'=>$this->ownerComponent,'ownerEntity'=>$this->ownerEntity,'ownerId'=>$this->ownerId,
            'contextComponent'=>$this->contextComponent,'contextEntity'=>$this->contextEntity,'contextId'=>$this->contextId,
            'photoType'=>$this->photoType,'originalPath'=>$this->originalPath,'mimeType'=>$this->mimeType,'width'=>$this->width,'height'=>$this->height,
            'fileSize'=>$this->fileSize,'isPrimary'=>$this->isPrimary,'status'=>$this->status,'season'=>$this->season,'title'=>$this->title,'description'=>$this->description,
        ];
    }
}
