<?php

namespace Boy132\MinecraftModrinth\Filament\Components;

use Filament\Schemas\Components\Component;

class TabsComponent extends Component
{
    protected string $view = 'minecraft-modrinth::components.tabs';

    public static function make(): static
    {
        return app(static::class);
    }
}
