<?php

namespace Boy132\PlayerCounter\Http\Controllers\Api\Client\Servers;

use App\Http\Controllers\Api\Client\ClientApiController;
use App\Models\Server;
use Boy132\PlayerCounter\Models\GameQuery;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[Group('Server - Players')]
class PlayerCounterController extends ClientApiController
{
    /**
     * Get query
     *
     * Returns query information.
     *
     * @throws HttpException
     */
    public function query(Server $server): JsonResponse
    {
        $data = $this->runQuery($server);

        return response()->json(array_except($data, 'players'));
    }

    /**
     * Get players
     *
     * Returns the names of the current players.
     *
     * @throws HttpException
     */
    public function players(Server $server): JsonResponse
    {
        $data = $this->runQuery($server);

        if (!$data['players']) {
            abort(Response::HTTP_NOT_ACCEPTABLE, 'Server query has no player list');
        }

        /** @var string[] $players */
        $players = array_map(fn ($player) => $player['name'], $data['players']);

        return response()->json($players);
    }

    /** @return ?array{hostname: string, map: string, current_players: int, max_players: int, players: ?array<array{id: string, name: string}>} */
    private function runQuery(Server $server): ?array
    {
        if (!GameQuery::canRunQuery($server->allocation)) {
            abort(Response::HTTP_NOT_ACCEPTABLE, 'Server has invalid allocation');
        }

        if ($server->retrieveStatus()->isOffline()) {
            abort(Response::HTTP_NOT_ACCEPTABLE, 'Server is offline');
        }

        /** @var ?GameQuery $gameQuery */
        $gameQuery = $server->egg->gameQuery; // @phpstan-ignore property.notFound

        if (!$gameQuery) {
            abort(Response::HTTP_NOT_ACCEPTABLE, 'Server has no query');
        }

        return $gameQuery->runQuery($server->allocation);
    }
}
