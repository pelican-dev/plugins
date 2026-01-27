<?php

namespace Boy132\PlayerCounter\Extensions\Query\Schemas;

use xPaw\SourceQuery\SourceQuery;

class GoldSourceQueryTypeSchema extends SourceQueryTypeSchema
{
    public function getId(): string
    {
        return 'goldsrc';
    }

    public function getName(): string
    {
        return 'GoldSrc';
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: array<array{id: string, name: string}>} */
    public function process(string $ip, int $port): ?array
    {
        return $this->run($ip, $port, SourceQuery::GOLDSOURCE);
    }
}
