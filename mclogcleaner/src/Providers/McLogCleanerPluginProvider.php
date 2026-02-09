<?php

namespace JuggleGaming\McLogCleaner\Providers;

use Illuminate\Support\ServiceProvider;
use App\Enums\HeaderActionPosition;
use App\Filament\Server\Pages\Console;
use JuggleGaming\McLogCleaner\Filament\Components\Actions\McLogCleanAction;

class McLogCleanerPluginProvider extends ServiceProvider {

    public function register(): void {
        Console::registerCustomHeaderActions(HeaderActionPosition::Before, McLogCleanAction::make());
    }

    public function boot(): void {}

}
