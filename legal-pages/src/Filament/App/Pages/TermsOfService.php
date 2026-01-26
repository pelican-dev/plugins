<?php

namespace Boy132\LegalPages\Filament\App\Pages;

use Boy132\LegalPages\Enums\LegalPageType;
use Filament\Panel;

class TermsOfService extends BaseLegalPage
{
    public static function getSlug(?Panel $panel = null): string
    {
        return LegalPageType::TermsOfService->getId();
    }

    public function getPageType(): LegalPageType
    {
        return LegalPageType::TermsOfService;
    }
}
