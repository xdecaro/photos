<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Preset;
final class PresetDefinition { public function __construct(public readonly string $id, public readonly float $aspectRatio, public readonly ?int $width=null, public readonly ?int $height=null, public readonly bool $preserveAlpha=false) {} }
