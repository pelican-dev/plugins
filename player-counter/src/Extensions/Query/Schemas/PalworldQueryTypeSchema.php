<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

use App\Models\Server;
use Boy132\PlayerCounter\Extensions\Query\QueryTypeSchemaInterface;
use Exception;
use Illuminate\Support\Facades\Http;

class PalworldQueryTypeSchema implements QueryTypeSchemaInterface
{
    public function getId(): string
    {
        return 'palworld';
    }

    public function getName(): string
    {
        return 'Palworld';
    }

    public function process(Server $server, string $ip, int $port): ?array
    {
        $adminPassword = $server->variables()
            ->where('env_variable', 'ADMIN_PASSWORD')
            ->first()?->server_value;

        if (!$adminPassword) {
            return null;
        }

        try {
            $http = Http::acceptJson()
                ->timeout(5)
                ->withBasicAuth('admin', $adminPassword)
                ->throw()
                ->baseUrl("http://{$ip}:{$port}");

            $info = $http->get('v1/api/info')->json();
            $metrics = $http->get('v1/api/metrics')->json();
            $players = $http->get('v1/api/players')->json();

            $players = $players['players'] ?? [];

            return [
                'hostname' => $info['servername'],
                'map' => 'Palpagos Islands',
                'current_players' => $metrics['currentplayernum'],
                'max_players' => $metrics['maxplayernum'],
                'players' => array_map(fn ($player) => ['id' => (string) ($player['playerId'] ?? $player['userId']), 'name' => (string) $player['name']], $players),
            ];

        } catch (Exception $exception) {
            report($exception);

            return null;
        }
    }
}
