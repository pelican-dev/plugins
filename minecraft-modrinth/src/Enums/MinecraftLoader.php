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

    public function getLabel(): string
    {
        return Str::title($this->name);
    }

    public static function fromServer(Server $server): ?self
    {
        $server->loadMissing('egg');

        /**
        * @var string[] $tags
        */
        $tags = $server->egg->tags ?? [];


        return self::fromTags($tags);
    }

    /**
     * @param  string[]  $tags
     */
    public static function fromTags(array $tags): ?self
    {
        if (!in_array('minecraft', $tags)) {
            return null;
        }

        return match (true) {
            in_array('neoforge', $tags) || in_array('neoforged', $tags) => self::NeoForge,
            in_array('forge', $tags) => self::Forge,
            in_array('fabric', $tags) => self::Fabric,
            in_array('quilt', $tags) => self::Quilt,

            in_array('folia', $tags) => self::Folia,
            in_array('purpur', $tags) => self::Purpur,
            in_array('pufferfish', $tags) => self::Pufferfish,
            in_array('paper', $tags) || in_array('papermc', $tags) => self::Paper,
            in_array('spigot', $tags) || in_array('spigotmc', $tags) => self::Spigot,
            in_array('bukkit', $tags) => self::Bukkit,

            in_array('velocity', $tags) => self::Velocity,
            in_array('waterfall', $tags) => self::Waterfall,
            in_array('bungeecord', $tags) || in_array('bungee', $tags) => self::Bungeecord,

            default => null,
        };
    }
}
