<?php

namespace Boy132\LegalPages\Filament\App\Pages;

use Boy132\LegalPages\Enums\LegalPageType;
use Filament\Panel;

class PrivacyPolicy extends BaseLegalPage
{
    public static function getSlug(?Panel $panel = null): string
    {
        return LegalPageType::PrivacyPolicy->getId();
    }

    public function getPageType(): LegalPageType
    {
        return LegalPageType::PrivacyPolicy;
    }
}
