<?php
declare(strict_types=1);

namespace xdecaro\Component\Photos\Site\Value;

use InvalidArgumentException;
use xdecaro\Core\Integration\EntityReference;

final class PhotoReference
{
    private function __construct(
        private readonly string $component,
        private readonly string $entity,
        private readonly string $id,
    ) {}

    public static function fromParts(string $component, string $entity, string $id): self
    {
        $component = trim($component);
        $entity = trim($entity);
        $id = trim($id);

        if (!preg_match('/^com_[a-z0-9][a-z0-9_]*$/', $component)) {
            throw new InvalidArgumentException('Invalid Joomla component element.');
        }
        if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/', $entity)) {
            throw new InvalidArgumentException('Invalid entity type.');
        }
        if ($id === '' || !preg_match('/^[A-Za-z0-9][A-Za-z0-9._:-]{0,127}$/', $id)) {
            throw new InvalidArgumentException('Invalid entity ID.');
        }

        return new self($component, $entity, $id);
    }

    public static function optionalContext(?string $component, ?string $entity, ?string $id): ?self
    {
        $values = [$component, $entity, $id];
        $present = array_map(static fn($v): bool => $v !== null && trim((string) $v) !== '', $values);
        if (!in_array(true, $present, true)) {
            return null;
        }
        if (in_array(false, $present, true)) {
            throw new InvalidArgumentException('Context reference must be fully specified or absent.');
        }
        return self::fromParts((string) $component, (string) $entity, (string) $id);
    }

    public function component(): string { return $this->component; }
    public function entity(): string { return $this->entity; }
    public function id(): string { return $this->id; }

    public function toEntityReference(): EntityReference
    {
        return new EntityReference($this->component, $this->entity, $this->id);
    }

    public function toArray(): array
    {
        return ['component' => $this->component, 'entity' => $this->entity, 'id' => $this->id];
    }
}
