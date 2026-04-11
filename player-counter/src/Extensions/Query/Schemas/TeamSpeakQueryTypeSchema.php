<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

use App\Models\Allocation;
use Boy132\PlayerCounter\Extensions\Query\QueryTypeSchemaInterface;
use Exception;

class TeamSpeakQueryTypeSchema implements QueryTypeSchemaInterface
{
    public function getId(): string
    {
        return 'teamspeak';
    }

    public function getName(): string
    {
        return 'TeamSpeak 3 (ServerQuery)';
    }

    public function resolvePort(Allocation $allocation): ?int
    {
        $server = $allocation->server;
        if (!$server) {
            return null;
        }

        $variable = $server->variables()->where('env_variable', 'QUERY_PORT')->first();
        if (!$variable) {
            return null;
        }

        $port = (int) ($variable->server_value ?: $variable->default_value);

        return $port > 0 ? $port : null;
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: ?array<array{id: string, name: string}>} */
    public function process(string $ip, int $port): ?array
    {
        $socket = null;

        try {
            $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
            if ($socket === false) {
                throw new Exception("Could not connect to TeamSpeak ServerQuery: $errstr ($errno)");
            }

            stream_set_timeout($socket, 5);

            // Read greeting: "TS3", welcome text, empty line
            $greeting = fgets($socket);
            if (!str_starts_with(trim($greeting), 'TS3')) {
                throw new Exception('Not a TeamSpeak 3 ServerQuery interface');
            }
            fgets($socket); // welcome line
            fgets($socket); // empty line

            // Select first virtual server
            fwrite($socket, "use sid=1\n");
            $this->readUntilError($socket);

            // Get server info
            fwrite($socket, "serverinfo\n");
            $infoLine = $this->readUntilError($socket);

            // Get client list
            fwrite($socket, "clientlist\n");
            $clientLine = $this->readUntilError($socket);

            fwrite($socket, "quit\n");

            $info = $this->parseLine($infoLine);
            $clients = $this->parseClientList($clientLine);

            return [
                'hostname'        => $this->unescape($info['virtualserver_name'] ?? 'Unknown'),
                'map'             => 'TeamSpeak',
                'current_players' => count($clients),
                'max_players'     => (int) ($info['virtualserver_maxclients'] ?? 0),
                'players'         => array_map(fn ($c) => [
                    'id'   => $c['clid'] ?? '0',
                    'name' => $this->unescape($c['client_nickname'] ?? 'Unknown'),
                ], $clients),
            ];
        } catch (Exception $exception) {
            report($exception);
        } finally {
            if (!empty($socket)) {
                fclose($socket);
            }
        }

        return null;
    }

    private function readUntilError($socket): string
    {
        $data = '';
        while (!feof($socket)) {
            $line = fgets($socket);
            if ($line === false) {
                break;
            }
            $trimmed = trim($line);
            if (str_starts_with($trimmed, 'error ')) {
                break;
            }
            if ($trimmed !== '') {
                $data = $trimmed;
            }
        }
        return $data;
    }

    /** @return array<string, string> */
    private function parseLine(string $line): array
    {
        $result = [];
        foreach (explode(' ', $line) as $token) {
            if (str_contains($token, '=')) {
                [$key, $value] = explode('=', $token, 2);
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /** @return array<int, array<string, string>> */
    private function parseClientList(string $line): array
    {
        $clients = [];
        foreach (explode('|', $line) as $entry) {
            $client = $this->parseLine($entry);
            // Skip ServerQuery clients (client_type=1)
            if (($client['client_type'] ?? '0') === '1') {
                continue;
            }
            $clients[] = $client;
        }
        return $clients;
    }

    private function unescape(string $value): string
    {
        return str_replace(
            ['\\s', '\\p', '\\/', '\\\\', '\\n', '\\r', '\\t', '\\a', '\\b', '\\f', '\\v'],
            [' ',   '|',   '/',   '\\',   "\n",  "\r",  "\t",  "\x07", "\x08", "\x0C", "\x0B"],
            $value
        );
    }
}
