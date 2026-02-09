<?php

namespace JuggleGaming\McLogCleaner\Enums;

use App\Models\Server;

enum EggFeature: string
{
    case Check = 'mclogcleaner';

    public static function fromServer(Server $server): ?self
    {
        $server->loadMissing('egg');

        $features = $server->egg->features ?? [];

        if (in_array(self::Check->value, $features, true)) {
            return self::Check;
        }

        return null;
    }

    public static function serverSupportsLogCleaner(Server $server): bool
    {
        return self::fromServer($server) !== null;
    }
}
