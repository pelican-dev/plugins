<?php

namespace Boy132\PlayerCounter\Providers;

use App\Enums\ConsoleWidgetPosition;
use App\Filament\Server\Pages\Console;
use App\Models\Egg;
use App\Models\Role;
use Boy132\PlayerCounter\Extensions\Query\QueryTypeService;
use Boy132\PlayerCounter\Extensions\Query\Schemas\Arma3QueryTypeSchema;
use Boy132\PlayerCounter\Extensions\Query\Schemas\ArmaReforgerQueryTypeSchema;
use Boy132\PlayerCounter\Extensions\Query\Schemas\CitizenFXQueryTypeSchema;
use Boy132\PlayerCounter\Extensions\Query\Schemas\GoldSourceQueryTypeSchema;
use Boy132\PlayerCounter\Extensions\Query\Schemas\MinecraftBedrockQueryTypeSchema;
use Boy132\PlayerCounter\Extensions\Query\Schemas\MinecraftJavaQueryTypeSchema;
use Boy132\PlayerCounter\Extensions\Query\Schemas\SourceQueryTypeSchema;
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

        $this->app->singleton(QueryTypeService::class, function () {
            $service = new QueryTypeService();

            // Default Query types
            $service->register(new SourceQueryTypeSchema());
            $service->register(new GoldSourceQueryTypeSchema());
            $service->register(new MinecraftJavaQueryTypeSchema());
            $service->register(new MinecraftBedrockQueryTypeSchema());
            $service->register(new CitizenFXQueryTypeSchema());
            $service->register(new Arma3QueryTypeSchema());
            $service->register(new ArmaReforgerQueryTypeSchema());

            return $service;
        });
    }

    public function boot(): void
    {
        Egg::resolveRelationUsing('gameQuery', fn (Egg $egg) => $egg->hasOneThrough(GameQuery::class, EggGameQuery::class, 'egg_id', 'id', 'id', 'game_query_id'));
    }
}
