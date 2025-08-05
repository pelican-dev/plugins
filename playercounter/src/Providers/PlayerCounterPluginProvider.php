<?php

namespace Boy132\PlayerCounter\Providers;

use App\Enums\ConsoleWidgetPosition;
use App\Filament\Server\Pages\Console;
use Boy132\PlayerCounter\Filament\Server\Widgets\ServerPlayerWidget;
use Illuminate\Support\ServiceProvider;

class PlayerCounterPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        Console::registerCustomWidgets(ConsoleWidgetPosition::AboveConsole, [ServerPlayerWidget::class]);
    }

    public function boot(): void {}
}
