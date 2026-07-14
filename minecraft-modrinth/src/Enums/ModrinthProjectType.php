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
            self::Mod => trans('minecraft-modrinth::strings.minecraft_mods'),
            self::Plugin => trans('minecraft-modrinth::strings.minecraft_plugins'),
        };
    }

    public function getFolder(): string
    {
        return match ($this) {
            self::Mod => 'mods',
            self::Plugin => 'plugins',
        };
    }

    /** @return ModrinthProjectType[] */
    public static function fromServer(Server $server): array
    {
        $server->loadMissing('egg');

        $features = $server->egg->features ?? [];
        $tags = $server->egg->tags ?? [];

        $projectTypes = [];

        if (in_array('modrinth_plugins', $features) || (in_array('minecraft', $tags) && in_array('plugins', $features))) {
            $projectTypes[] = self::Plugin;
        }

        if (in_array('modrinth_mods', $features) || (in_array('minecraft', $tags) && in_array('mods', $features))) {
            $projectTypes[] = self::Mod;
        }

        return $projectTypes;
    }
}
