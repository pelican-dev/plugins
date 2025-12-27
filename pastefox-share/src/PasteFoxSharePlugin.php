<?php

namespace FlexKleks\PasteFoxShare;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PasteFoxSharePlugin implements Plugin
{
    public function getId(): string
    {
        return 'pastefox-share';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}
}
