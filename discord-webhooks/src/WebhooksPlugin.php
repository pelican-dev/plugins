<?php

namespace Notjami\Webhooks;

use Filament\Contracts\Plugin;
use Filament\Panel;

class WebhooksPlugin implements Plugin
{
    public function getId(): string
    {
        return 'discord-webhooks';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Notjami\\Webhooks\\Filament\\$id\\Resources");
    }

    public function boot(Panel $panel): void {}
}
