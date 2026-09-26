<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Preset;

use InvalidArgumentException;

final class PresetRegistry
{
    /** @var array<string,PresetDefinition> */
    private array $presets;

    public function __construct()
    {
        $this->presets = [
            'avatar' => new PresetDefinition('avatar', 512, 512, 1.0, true),
            'passport' => new PresetDefinition('passport', 600, 800, 0.75, true),
            'thumbnail' => new PresetDefinition('thumbnail', 320, 320, 1.0, true),
        ];
    }

    public function get(string $id): PresetDefinition
    {
        return $this->presets[$id] ?? throw new InvalidArgumentException('Unknown photo preset.');
    }

    public function all(): array { return $this->presets; }
}
