<?php

namespace Boy132\PlayerCounter\Extensions\Query;

use App\Models\Server;

interface ServerAwareQueryTypeSchemaInterface extends QueryTypeSchemaInterface
{
    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: ?array<array{id: string, name: string}>} */
    public function processWithServer(Server $server, string $ip, int $port): ?array;
}
