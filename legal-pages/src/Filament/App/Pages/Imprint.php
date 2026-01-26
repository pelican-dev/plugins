<?php

namespace Boy132\LegalPages\Filament\App\Pages;

use Boy132\LegalPages\Enums\LegalPageType;
use Filament\Panel;

class Imprint extends BaseLegalPage
{
    public static function getSlug(?Panel $panel = null): string
    {
        return LegalPageType::Imprint->getId();
    }

    public function getPageType(): LegalPageType
    {
        return LegalPageType::Imprint;
    }
}
