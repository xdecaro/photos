<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Value;

use InvalidArgumentException;
use xdecaro\Core\Integration\EntityReference;

final class PhotoReference
{
    private function __construct(public readonly string $component, public readonly string $entity, public readonly string $id) {}

    public static function fromParts(string $component, string $entity, string $id): self
    {
        $component = trim($component); $entity = trim($entity); $id = trim($id);
        if ($component === '' || $entity === '' || $id === '') throw new InvalidArgumentException('Entity reference requires component, entity and id.');
        if (!preg_match('/^com_[a-z0-9_]+$/', $component)) throw new InvalidArgumentException('Invalid component reference.');
        if (!preg_match('/^[a-z0-9_\-]+$/', $entity)) throw new InvalidArgumentException('Invalid entity reference.');
        return new self($component, $entity, $id);
    }

    public static function nullableContext(?string $component, ?string $entity, ?string $id): ?self
    {
        $parts = [$component, $entity, $id];
        $empty = array_map(static fn($v) => $v === null || trim((string)$v) === '', $parts);
        if ($empty === [true,true,true]) return null;
        if (in_array(true, $empty, true)) throw new InvalidArgumentException('Context must be completely empty or complete.');
        return self::fromParts((string)$component,(string)$entity,(string)$id);
    }

    public function toEntityReference(): EntityReference
    {
        return new EntityReference($this->component, $this->entity, $this->id);
    }
}
