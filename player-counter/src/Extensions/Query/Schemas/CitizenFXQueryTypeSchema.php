<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

use Boy132\PlayerCounter\Extensions\Query\QueryTypeSchemaInterface;
use Exception;
use Illuminate\Support\Facades\Http;

class CitizenFXQueryTypeSchema implements QueryTypeSchemaInterface
{
    public function getId(): string
    {
        return 'cfx';
    }

    public function getName(): string
    {
        return 'CitizenFX';
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: array<array{id: string, name: string}>} */
    public function process(string $ip, int $port): ?array
    {
        try {
            $this->resolveSRV($ip, $port);

            $http = Http::acceptJson()
                ->connectTimeout(5)
                ->throw()
                ->baseUrl("http://$ip:$port/");

            $info = $http->get('dynamic.json')->json();
            $players = $http->get('players.json')->json();

            if (!$info || !$players) {
                return null;
            }

            return [
                'hostname' => $info['hostname'],
                'map' => $info['mapname'],
                'current_players' => $info['clients'],
                'max_players' => $info['sv_maxclients'],
                'players' => array_map(fn ($player) => ['id' => (string) $player['id'], 'name' => (string) $player['name']], $players),
            ];
        } catch (Exception $exception) {
            report($exception);
        }

        return null;
    }

    private function resolveSRV(string &$ip, int &$port): void
    {
        if (is_ip($ip)) {
            return;
        }

        $record = dns_get_record('_cfx._udp.' . $ip, DNS_SRV);

        if (!$record) {
            return;
        }

        if ($record[0]['target']) {
            $ip = $record[0]['target'];
        }

        if ($record[0]['port']) {
            $port = (int) $record[0]['port'];
        }
    }
}
