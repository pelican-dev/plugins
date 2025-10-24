<?php

namespace Boy132\PlayerCounter\Filament\Server\Widgets;

use App\Filament\Server\Components\SmallStatBlock;
use App\Models\Server;
use Boy132\PlayerCounter\Models\EggGameQuery;
use Boy132\PlayerCounter\Models\GameQuery;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ServerPlayerWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return !$server->isInConflictState() && $server->allocation && static::getGameQuery()->exists() && !$server->retrieveStatus()->isOffline();
    }

    protected function getStats(): array
    {
        /** @var ?GameQuery $gameQuery */
        $gameQuery = static::getGameQuery()->first();

        if (!$gameQuery) {
            return [];
        }

        /** @var Server $server */
        $server = Filament::getTenant();

        $data = $gameQuery->runQuery($server->allocation);

        return [
            SmallStatBlock::make(trans('player-counter::query.hostname'), $data['gq_hostname'] ?? trans('player-counter::query.unknown')),
            SmallStatBlock::make(trans('player-counter::query.players'), ($data['gq_numplayers'] ?? '?') . ' / ' . ($data['gq_maxplayers'] ?? '?')),
            SmallStatBlock::make(trans('player-counter::query.map'), $data['gq_mapname'] ?? trans('player-counter::query.unknown')),
        ];
    }

    protected static function getGameQuery(): HasOneThrough
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return $server->egg->hasOneThrough(GameQuery::class, EggGameQuery::class, 'egg_id', 'id', 'id', 'game_query_id');
    }
}
