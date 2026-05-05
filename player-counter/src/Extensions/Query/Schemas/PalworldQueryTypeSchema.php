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
            $base = "http://{$ip}:{$port}";
            [$playersResp, $metricsResp] = Http::pool(fn ($pool) => [
                $pool->timeout(5)->withBasicAuth('admin', $adminPassword)->get("{$base}/v1/api/players"),
                $pool->timeout(5)->withBasicAuth('admin', $adminPassword)->get("{$base}/v1/api/metrics"),
            ]);

            if (!$playersResp->ok()) {
                return null;
            }

            $data = $playersResp->json();
            $players = array_map(fn ($p) => [
                'id' => $p['playeruid'] ?? $p['steamid'] ?? '',
                'name' => $p['name'] ?? '',
            ], $data['players'] ?? []);

            $maxPlayers = $metricsResp->ok() ? ($metricsResp->json()['maxplayernum'] ?? 32) : 32;

            return [
                'hostname' => $server->name,
                'map' => 'Palpagos Islands',
                'current_players' => count($players),
                'max_players' => $maxPlayers,
                'players' => $players,
            ];

        } catch (Exception $exception) {
            report($exception);

            return null;
        }
    }
}
