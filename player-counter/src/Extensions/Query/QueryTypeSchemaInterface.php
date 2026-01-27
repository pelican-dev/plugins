<?php

namespace Boy132\PlayerCounter\Extensions\Query;

interface QueryTypeSchemaInterface
{
    public function getId(): string;

    public function getName(): string;

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: array<array{id: string, name: string}>} */
    public function process(string $ip, int $port): ?array;
}
