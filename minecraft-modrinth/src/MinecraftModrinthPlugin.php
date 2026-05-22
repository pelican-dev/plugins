<?php

namespace Boy132\MinecraftModrinth;

use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Panel;

class MinecraftModrinthPlugin implements Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'minecraft-modrinth';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "Boy132\\MinecraftModrinth\\Filament\\$id\\Pages");
    }

    public function boot(Panel $panel): void {}
}
