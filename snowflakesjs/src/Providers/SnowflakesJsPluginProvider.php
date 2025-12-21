<?php

namespace notCharles\SnowflakesJs\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SnowflakesJsPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        $enabled = config('snowflakesjs.enabled');
        $size = config('snowflakesjs.size');
        $speed = config('snowflakesjs.speed');
        $opacity = config('snowflakesjs.opacity');
        $density = config('snowflakesjs.density');
        $quality = config('snowflakesjs.quality');

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
