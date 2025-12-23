<?php

namespace Boy132\PlayerCounter\Providers;

use App\Enums\ConsoleWidgetPosition;
use App\Filament\Server\Pages\Console;
use App\Models\Egg;
use App\Models\Role;
use Boy132\PlayerCounter\Filament\Server\Widgets\ServerPlayerWidget;
use Boy132\PlayerCounter\Models\EggGameQuery;
use Boy132\PlayerCounter\Models\GameQuery;
use Illuminate\Support\ServiceProvider;

class PlayerCounterPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        Role::registerCustomDefaultPermissions('game_query');
        Role::registerCustomModelIcon('game_query', 'tabler-device-desktop-search');

        Console::registerCustomWidgets(ConsoleWidgetPosition::AboveConsole, [ServerPlayerWidget::class]);
    }

    public function boot(): void
    {
        Egg::resolveRelationUsing('gameQuery', fn (Egg $egg) => $egg->hasOneThrough(GameQuery::class, EggGameQuery::class, 'egg_id', 'id', 'id', 'game_query_id'));
    }
}
