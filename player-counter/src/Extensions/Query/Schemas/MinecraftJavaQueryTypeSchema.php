<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

use Boy132\PlayerCounter\Extensions\Query\QueryTypeSchemaInterface;
use Exception;
use xPaw\MinecraftPing;
use xPaw\MinecraftQuery;

class MinecraftJavaQueryTypeSchema implements QueryTypeSchemaInterface
{
    public function getId(): string
    {
        return 'minecraft_java';
    }

    public function getName(): string
    {
        return 'Minecraft (Java)';
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: array<array{id: string, name: string}>} */
    public function process(string $ip, int $port): ?array
    {
        $query = $this->tryQuery($ip, $port);
        if ($query) {
            return $query;
        }

        try {
            $ping = new MinecraftPing($ip, $port, 5, true);

            $data = $ping->Query();

            if (!$data) {
                return null;
            }

            return [                
                'hostname' => is_string($data['description']) ? $data['description'] : $data['description']['text'],
                'map' => 'world', // No map from MinecraftPing
                'current_players' => $data['players']['online'],
                'max_players' => $data['players']['max'],
                'players' => $data['players']['sample'],
            ];
        } catch (Exception $exception) {
            report($exception);
        } finally {
            if (isset($ping)) {
                $ping->Close();
            }
        }

        return null;
    }

    /** @return false|array{hostname: string, map: string, current_players: int, max_players: int, players: array<array{id: string, name: string}>} */
    protected function tryQuery(string $ip, int $port): false|array
    {
        $query = new MinecraftQuery();

        try {
            $query->Connect($ip, $port, 5, true);

            $info = $query->GetInfo();
            $players = $query->GetPlayers();

            if (!$info || !$players) {
                return false;
            }

            return [
                'hostname' => $info['HostName'],
                'map' => $info['Map'],
                'current_players' => $info['Players'],
                'max_players' => $info['MaxPlayers'],
                'players' => array_map(fn ($player) => ['id' => $player['Id'], 'name' => $player['Name']], $players),
            ];
        } catch (Exception $exception) {
            report($exception);
        }

        return false;
    }
}
