<?php

namespace Boy132\MinecraftModrinth\Enums;

use App\Models\Server;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum MinecraftLoader: string implements HasLabel
{
    case NeoForge = 'neoforge';
    case Forge = 'forge';
    case Fabric = 'fabric';
    case Quilt = 'quilt';
    case Paper = 'paper';
    case Purpur = 'purpur';
    case Folia = 'folia';
    case Pufferfish = 'pufferfish';
    case Spigot = 'spigot';
    case Bukkit = 'bukkit';
    case Velocity = 'velocity';
    case Bungeecord = 'bungeecord';
    case Waterfall = 'waterfall';

    /**
     * Get the label for the loader.
     */
    public function getLabel(): string
    {
        return Str::title($this->name);
    }

    /**
     * Get the loader instance from a server.
     */
    public static function fromServer(Server $server): ?self
    {
        $server->loadMissing('egg');

        /** @var  string[]  $tags */
        $tags = $server->egg->tags ?? [];

        return self::fromTags($tags);
    }

    public static function fromTags(array $tags): ?self
    {
        if (! in_array('minecraft', $tags)) {
            return null;
        }

        return match (true) {
            // Mod Loaders
            in_array('neoforge', $tags) || in_array('neoforged', $tags) => self::NeoForge,
            in_array('forge', $tags) => self::Forge,
            in_array('fabric', $tags) => self::Fabric,
            in_array('quilt', $tags) => self::Quilt,

            // Server Software (Forks)
            in_array('folia', $tags) => self::Folia,
            in_array('purpur', $tags) => self::Purpur,
            in_array('pufferfish', $tags) => self::Pufferfish,
            in_array('paper', $tags) || in_array('papermc', $tags) => self::Paper,
            in_array('spigot', $tags) || in_array('spigotmc', $tags) => self::Spigot,
            in_array('bukkit', $tags) => self::Bukkit,

            // Proxy Software
            in_array('velocity', $tags) => self::Velocity,
            in_array('waterfall', $tags) => self::Waterfall,
            in_array('bungeecord', $tags) || in_array('bungee', $tags) => self::Bungeecord,

            default => null,
        };
    }
}
