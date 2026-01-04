<?php

namespace Boy132\Snowflakes\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SnowflakesPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        $isEnabled = config('snowflakes.enabled');

        // Check user customization if the user is authenticated
        if ($isEnabled && auth()->check()) {
            $isEnabled = user()?->getCustomization('snowflakes_enabled') ?? true;
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            fn () => $isEnabled ? Blade::render(<<<'HTML'
            <script>
            window.SnowflakeConfig = {
                size: {{ $size }},
                speed: {{ $speed }},
                opacity: {{ $opacity }},
                density: {{ $density }},
                quality: {{ $quality }},
                index: 9,
                mount: document.body,
            };
            </script>
            <script src="https://cdn.jsdelivr.net/gh/nextapps-de/snowflake@master/snowflake.min.js"></script>
            HTML, config('snowflakes')) : ''
        );
    }
}
