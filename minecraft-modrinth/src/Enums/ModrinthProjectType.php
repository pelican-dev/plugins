<?php

namespace Boy132\MinecraftModrinth\Enums;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;

enum ModrinthProjectType: string implements HasLabel
{
    case Mod = 'mod';
    case Plugin = 'plugin';

    public function getLabel(): string
    {
        return match ($this) {
            self::Mod => 'Minecraft Mods',
            self::Plugin => 'Minecraft Plugins',
        };
    }

    public function getFolder(): string
    {
        return match ($this) {
            self::Mod => 'mods',
            self::Plugin => 'plugins',
        };
    }

    public static function fromServer(Server $server): ?ModrinthProjectType
    {
        $features = $server->egg->features ?? [];
        $tags = $server->egg->tags ?? [];

        if (in_array('modrinth_plugins', $features) || (in_array('minecraft', $tags) && in_array('plugins', $features))) {
            return self::Plugin;
        }

        if (in_array('modrinth_mods', $features) || (in_array('minecraft', $tags) && in_array('mods', $features))) {
            return self::Mod;
        }

        return null;
    }
}
