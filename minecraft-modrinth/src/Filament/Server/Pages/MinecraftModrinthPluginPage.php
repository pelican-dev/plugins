<?php

namespace Boy132\MinecraftModrinth\Filament\Server\Pages;

use Boy132\MinecraftModrinth\Enums\ModrinthProjectType;

class MinecraftModrinthPluginPage extends MinecraftModrinthProjectPage
{
    protected static ?ModrinthProjectType $modrinthProjectType = ModrinthProjectType::Plugin;

    protected static ?string $slug = 'modrinth_plugins';
}
