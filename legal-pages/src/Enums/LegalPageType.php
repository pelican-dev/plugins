<?php

namespace Boy132\LegalPages\Enums;

use Boy132\LegalPages\Filament\App\Pages\Imprint;
use Boy132\LegalPages\Filament\App\Pages\PrivacyPolicy;
use Boy132\LegalPages\Filament\App\Pages\TermsOfService;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum LegalPageType: string implements HasLabel
{
    case Imprint = Imprint::class;
    case TermsOfService = TermsOfService::class;
    case PrivacyPolicy = PrivacyPolicy::class;

    public function getId(): string
    {
        return Str::snake($this->name);
    }

    public function getLabel(): string
    {
        return trans('legal-pages::strings.' . $this->getId());
    }

    public function getUrl(): string
    {
        return '/' . Str::slug($this->getId());
    }

    /** @return class-string */
    public function getClass(): string
    {
        return $this->value;
    }
}
