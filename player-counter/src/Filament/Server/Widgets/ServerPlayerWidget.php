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

        return !$server->isInConflictState() && $server->allocation && $server->egg->gameQuery()->exists() && !$server->retrieveStatus()->isOffline(); // @phpstan-ignore method.notFound
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

        $data = $gameQuery->runQuery($server->allocation);

        return [
            SmallStatBlock::make(trans('player-counter::query.hostname'), $data['gq_hostname'] ?? trans('player-counter::query.unknown')),
            SmallStatBlock::make(trans('player-counter::query.players'), ($data['gq_numplayers'] ?? '?') . ' / ' . ($data['gq_maxplayers'] ?? '?')),
            SmallStatBlock::make(trans('player-counter::query.map'), $data['gq_mapname'] ?? trans('player-counter::query.unknown')),
        ];
    }
}
