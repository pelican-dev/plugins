<?php

namespace FlexKleks\ServerFolders;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders\Pages\ViewServerFolder;
use FlexKleks\ServerFolders\Models\ServerFolder;

class ServerFoldersPlugin implements Plugin
{
    public function getId(): string
    {
        return 'server-folders';
    }

    public function register(Panel $panel): void
    {
        if ($panel->getId() === 'app') {
            $panel->navigation(true);
        }

        $id = str($panel->getId())->title();

        $panel->discoverResources(
            plugin_path($this->getId(), "src/Filament/$id/Resources"),
            "FlexKleks\\ServerFolders\\Filament\\$id\\Resources"
        );
    }

    public function boot(Panel $panel): void {}
}
