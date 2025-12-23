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
        $enabled = config('snowflakes.enabled');
        $size = config('snowflakes.size');
        $speed = config('snowflakes.speed');
        $opacity = config('snowflakes.opacity');
        $density = config('snowflakes.density');
        $quality = config('snowflakes.quality');

        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_START,
            fn () => Blade::render(<<<'HTML'
            <script>
            window.SnowflakeConfig = {
                start: {{ $enabled }},
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
            HTML, [
                'enabled' => $enabled,
                'size' => $size,
                'speed' => $speed,
                'opacity' => $opacity,
                'density' => $density,
                'quality' => $quality,
                ])
        );
    }
}
