<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

use Boy132\PlayerCounter\Extensions\Query\QueryTypeSchemaInterface;
use Exception;
use xPaw\SourceQuery\SourceQuery;

class SourceQueryTypeSchema implements QueryTypeSchemaInterface
{
    public function getId(): string
    {
        return 'source';
    }

    public function getName(): string
    {
        return 'Source';
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: array<array{id: string, name: string}>} */
    public function process(string $ip, int $port): ?array
    {
        return $this->run($ip, $port, SourceQuery::SOURCE);
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: array<array{id: string, name: string}>} */
    protected function run(string $ip, int $port, int $engine): ?array
    {
        $query = new SourceQuery();

        try {
            $query->Connect($ip, $port, 5, $engine);

            $info = $query->GetInfo();

            if ($info === false) {
                return null;
            }

            $players = $query->GetPlayers() ?: [];

            return [
                'hostname' => (string) ($info['HostName'] ?? 'Unknown'),
                'map' => (string) ($info['Map'] ?? 'Unknown'),
                'current_players' => (int) ($info['Players'] ?? 0),
                'max_players' => (int) ($info['MaxPlayers'] ?? 0),
                'players' => array_map(fn ($player) => [
                    'id' => (string) ($player['Id'] ?? ''),
                    'name' => (string) ($player['Name'] ?? 'Unknown'),
                ], $players),
            ];
        } catch (Exception $exception) {
            report($exception);
        } finally {
            $query->Disconnect();
        }

        return null;
    }
}
