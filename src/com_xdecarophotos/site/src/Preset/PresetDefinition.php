<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Preset;

final class PresetDefinition
{
    public function __construct(
        public readonly string $id,
        public readonly int $width,
        public readonly int $height,
        public readonly float $aspectRatio,
        public readonly bool $cropDriven,
    ) {}
}
