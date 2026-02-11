<?php

namespace Boy132\MinecraftModrinth\Providers;

use Illuminate\Support\ServiceProvider;

class MinecraftModrinthPluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(
            plugin_path('minecraft-modrinth', 'resources/views'),
            'minecraft-modrinth'
        );
    }
}
