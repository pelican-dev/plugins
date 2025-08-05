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
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return !$server->isInConflictState() && $server->allocation && static::getGameQuery()->exists() && !$server->retrieveStatus()->isOffline();
    }

    protected function getStats(): array
    {
        $gameQuery = static::getGameQuery()->first();

        if (!$gameQuery) {
            return [];
        }

        /** @var Server $server */
        $server = Filament::getTenant();

        $data = $gameQuery->runQuery($server->allocation);

        return [
            SmallStatBlock::make(trans('playercounter::query.hostname'), $data['gq_hostname'] ?? trans('playercounter::query.unknown')),
            SmallStatBlock::make(trans('playercounter::query.players'), ($data['gq_numplayers'] ?? '?') . ' / ' . ($data['gq_maxplayers'] ?? '?')),
            SmallStatBlock::make(trans('playercounter::query.map'), $data['gq_mapname'] ?? trans('playercounter::query.unknown')),
        ];
    }

    private static function getGameQuery(): HasOneThrough
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return $server->egg->hasOneThrough(GameQuery::class, EggGameQuery::class, 'egg_id', 'id', 'id', 'game_query_id');
    }
}
