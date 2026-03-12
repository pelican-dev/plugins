<?php

namespace Ebnater\BackupTemplates;

use Filament\Contracts\Plugin;
use Filament\Panel;

class BackupTemplatesPlugin implements Plugin
{
    public function getId(): string
    {
        return 'backup-templates';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Ebnater\\BackupTemplates\\Filament\\$id\\Resources");
    }

    public function boot(Panel $panel): void {}
}
