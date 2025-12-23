<?php

namespace Boy132\LegalPages;

use Boy132\LegalPages\Enums\LegalPageType;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;

class LegalPagesPlugin implements Plugin
{
    public function getId(): string
    {
        return 'legal-pages';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "Boy132\\LegalPages\\Filament\\$id\\Pages");
    }

    public function boot(Panel $panel): void {}

    public static function Save(LegalPageType|string $type, ?string $contents): bool
    {
        if ($type instanceof LegalPageType) {
            $type = $type->getId();
        }

        $path = $type . '.md';

        if (!$contents) {
            return Storage::delete($path);
        }

        return Storage::put($path, $contents);
    }

    public static function Load(LegalPageType|string $type): ?string
    {
        if ($type instanceof LegalPageType) {
            $type = $type->getId();
        }

        return Storage::get($type . '.md');
    }
}
