<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

use App\Models\Server;
use Boy132\PlayerCounter\Extensions\Query\ServerAwareQueryTypeSchemaInterface;
use Illuminate\Support\Facades\Http;

class PalworldQueryTypeSchema implements ServerAwareQueryTypeSchemaInterface
{
    public function getId(): string
    {
        return 'palworld';
    }

    public function getName(): string
    {
        return 'Palworld';
    }

    // Fallback: no auth context available — cannot query REST API
    public function process(string $ip, int $port): ?array
    {
        return null;
    }

    public function processWithServer(Server $server, string $ip, int $port): ?array
    {
        $adminPassword = $server->variables()
            ->where('env_variable', 'ADMIN_PASSWORD')
            ->first()?->server_value;

        if (!$adminPassword) {
            return null;
        }

        try {
            $response = Http::timeout(5)
                ->withBasicAuth('admin', $adminPassword)
                ->get("http://{$ip}:{$port}/v1/api/players");

            if (!$response->ok()) {
                return null;
            }

            $data = $response->json();
            $players = array_map(fn ($p) => [
                'id'   => $p['playeruid'] ?? $p['steamid'] ?? '',
                'name' => $p['name'] ?? '',
            ], $data['players'] ?? []);

            // Fetch metrics for servername and max_players
            $metrics = Http::timeout(5)
                ->withBasicAuth('admin', $adminPassword)
                ->get("http://{$ip}:{$port}/v1/api/metrics")
                ->json();

            return [
                'hostname'        => $metrics['servername'] ?? $server->name,
                'map'             => 'Palpagos Islands',
                'current_players' => count($players),
                'max_players'     => $metrics['maxplayers'] ?? 32,
                'players'         => $players,
            ];
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}

