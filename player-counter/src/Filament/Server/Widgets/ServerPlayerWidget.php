<?php

namespace Boy132\PlayerCounter\Filament\Server\Widgets;

use App\Filament\Server\Components\SmallStatBlock;
use App\Models\Server;
use Boy132\PlayerCounter\Models\GameQuery;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;

class ServerPlayerWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        if ($server->isInConflictState()) {
            return false;
        }

        if (!GameQuery::canRunQuery($server->allocation)) {
            return false;
        }

        // @phpstan-ignore method.notFound
        if (!$server->egg->gameQuery()->exists()) {
            return false;
        }

        return !$server->retrieveStatus()->isOffline();
    }

    protected function getStats(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        /** @var ?GameQuery $gameQuery */
        $gameQuery = $server->egg->gameQuery; // @phpstan-ignore property.notFound

        if (!$gameQuery) {
            return [];
        }

        $data = $gameQuery->runQuery($server->allocation) ?? [];

        return [
            SmallStatBlock::make(trans('player-counter::query.hostname'), $data['hostname'] ?? trans('player-counter::query.unknown')),
            SmallStatBlock::make(trans('player-counter::query.players'), ($data['current_players'] ?? '?') . ' / ' . ($data['max_players'] ?? '?')),
            SmallStatBlock::make(trans('player-counter::query.map'), $data['map'] ?? trans('player-counter::query.unknown')),
        ];
    }
}
