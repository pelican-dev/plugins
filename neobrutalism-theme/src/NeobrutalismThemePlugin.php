<?php

namespace Boy132\NeobrutalismTheme;

use Filament\Contracts\Plugin;
use Filament\Panel;

class NeobrutalismThemePlugin implements Plugin
{
    public function getId(): string
    {
        return 'neobrutalism-theme';
    }

    public function register(Panel $panel): void
    {
        $panel->viteTheme('plugins/neobrutalism-theme/resources/css/theme.css');
    }

    public function boot(Panel $panel): void {}
}
