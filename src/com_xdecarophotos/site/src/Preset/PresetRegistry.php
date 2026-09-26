<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Site\Preset;
use InvalidArgumentException;
final class PresetRegistry
{
    public function get(string $id): PresetDefinition
    {
        return match($id){
            'avatar'=>new PresetDefinition('avatar',1.0),
            'passport'=>new PresetDefinition('passport',35/45),
            'thumbnail'=>new PresetDefinition('thumbnail',1.0,320,320),
            'card'=>new PresetDefinition('card',3/4),
            'sticker'=>new PresetDefinition('sticker',3/4),
            'logo'=>new PresetDefinition('logo',1.0,null,null,true),
            default=>throw new InvalidArgumentException('Unknown photo preset.'),
        };
    }
}
