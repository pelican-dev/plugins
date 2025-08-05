<?php

namespace Boy132\PlayerCounter;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PlayerCounterPlugin implements Plugin
{
    public function getId(): string
    {
        return 'playercounter';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\PlayerCounter\\Filament\\$id\\Resources");
        $panel->discoverWidgets(plugin_path($this->getId(), "src/Filament/$id/Widgets"), "Boy132\\PlayerCounter\\Filament\\$id\\Widgets");
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
