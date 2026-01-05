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

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
