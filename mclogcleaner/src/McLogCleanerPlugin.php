<?php

namespace JuggleGaming\McLogCleaner;

use Filament\Contracts\Plugin;
use Filament\Panel;

class McLogCleanerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'logcleaner';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void  {}
}
