<?php

namespace Boy132\LegalPages\Providers;

use App\Enums\CustomRenderHooks;
use App\Models\Role;
use Boy132\LegalPages\Enums\LegalPageType;
use Boy132\LegalPages\LegalPagesPlugin;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LegalPagesPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        Role::registerCustomPermissions([
            'legalPage' => [
                'view',
                'update',
            ],
        ]);
        Role::registerCustomModelIcon('legalPage', 'tabler-gavel');

        $footer = null;

        foreach (LegalPageType::cases() as $legalPageType) {
            $content = LegalPagesPlugin::Load($legalPageType);

            if ($content) {
                $label = $legalPageType->getLabel();
                $url = $legalPageType->getUrl();

                if (!$footer) {
                    $footer = "<x-filament::link href=\"$url\" target='_blank'>$label</x-filament::link>";
                } else {
                    $footer = "$footer | <x-filament::link href=\"$url\" target='_blank'>$label</x-filament::link>";
                }
            }
        }

        if ($footer) {
            FilamentView::registerRenderHook(CustomRenderHooks::FooterEnd->value, fn () => Blade::render("<div>$footer</div>"));
        }
    }
}
