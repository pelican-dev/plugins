<?php

namespace spolny\ArmaReforgerWorkshop;

use Filament\Contracts\Plugin;
use Filament\Panel;

class ArmaReforgerWorkshopPlugin implements Plugin
{
    public function getId(): string
    {
        return 'arma-reforger-workshop';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "spolny\\ArmaReforgerWorkshop\\Filament\\$id\\Pages");
    }

    public function boot(Panel $panel): void {}
}
