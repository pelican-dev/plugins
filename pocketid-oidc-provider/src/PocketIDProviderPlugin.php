<?php

namespace Ebnater\PocketIDProvider;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PocketIDProviderPlugin implements Plugin
{
    public function getId(): string
    {
        return 'pocketid-provider';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Ebnater\\PocketIDProvider\\Filament\\$id\\Resources");
    }

    public function boot(Panel $panel): void {}
}
