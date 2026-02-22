<?php

namespace Hampap\CzechLanguage;

use Filament\Contracts\Plugin;
use Filament\Panel;

class CzechLanguagePlugin implements Plugin
{
    public function getId(): string
    {
        return 'czech-language';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
