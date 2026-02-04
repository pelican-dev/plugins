    public static function fromServer(Server $server): ?MinecraftLoader
    {
        $server->loadMissing('egg');
        $tags = $server->egg->tags ?? [];

        return self::fromTags($tags);
    }

    /** @param string[] $tags */
    public static function fromTags(array $tags): ?MinecraftLoader
    {
        if (!in_array('minecraft', $tags)) {
            return null;
        }

        // Mapeo de Tags a Enums
        return match (true) {
            // Mods
            in_array('neoforge', $tags) || in_array('neoforged', $tags) => self::NeoForge,
            in_array('forge', $tags) => self::Forge,
            in_array('fabric', $tags) => self::Fabric,
            in_array('quilt', $tags) => self::Quilt,

            // Plugins (Forks de Paper/Spigot)
            in_array('folia', $tags) => self::Folia,
            in_array('purpur', $tags) => self::Purpur,
            in_array('pufferfish', $tags) => self::Pufferfish,
            in_array('paper', $tags) || in_array('papermc', $tags) => self::Paper,
            in_array('spigot', $tags) || in_array('spigotmc', $tags) => self::Spigot,
            in_array('bukkit', $tags) => self::Bukkit,

            // Proxies
            in_array('velocity', $tags) => self::Velocity,
            in_array('waterfall', $tags) => self::Waterfall,
            in_array('bungeecord', $tags) || in_array('bungee', $tags) => self::Bungeecord,

            default => null,
        };
    }
}
