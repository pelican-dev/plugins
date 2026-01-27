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
            $players = $query->GetPlayers();

            return [
                'hostname' => $info['HostName'],
                'map' => $info['Map'],
                'current_players' => $info['Players'],
                'max_players' => $info['MaxPlayers'],
                'players' => array_map(fn ($player) => ['id' => (string) $player['Id'], 'name' => (string) $player['Name']], $players),
            ];
        } catch (Exception $exception) {
            report($exception);
        } finally {
            $query->Disconnect();
        }

        return null;
    }
}
