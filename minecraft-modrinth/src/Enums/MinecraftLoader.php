<?php

namespace Boy132\MinecraftModrinth\Enums;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;

enum MinecraftLoader: string implements HasLabel
{
    case NeoForge = 'neoforge';
    case Forge = 'forge';
    case Fabric = 'fabric';
    case Quilt = 'quilt';
    case Paper = 'paper';
    case Velocity = 'velocity';
    case Bungeecord = 'bungeecord';

    public function getLabel(): string
    {
        return str($this->name)->title();
    }

    public static function fromServer(Server $server): ?MinecraftLoader
    {
        $server->loadMissing('egg');

        $tags = $server->egg->tags ?? [];

        return self::fromTags($tags);
    }

    /** @param string[] $tags */
    public static function fromTags(array $tags): ?MinecraftLoader
    {
        if (in_array('minecraft', $tags)) {
            if (in_array('neoforge', $tags) || in_array('neoforged', $tags)) {
                return self::NeoForge;
            }

            if (in_array('forge', $tags)) {
                return self::Forge;
            }

            if (in_array('fabric', $tags)) {
                return self::Fabric;
            }

            if (in_array('quilt', $tags)) {
                return self::Quilt;
            }

            if (in_array('bukkit', $tags) || in_array('spigot', $tags) || in_array('paper', $tags)) {
                return self::Paper;
            }

            if (in_array('velocity', $tags)) {
                return self::Velocity;
            }

            if (in_array('waterfall', $tags) || in_array('bungeecord', $tags)) {
                return self::Bungeecord;
            }
        }

        return null;
    }
}
