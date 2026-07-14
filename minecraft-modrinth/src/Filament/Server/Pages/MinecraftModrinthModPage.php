<?php

namespace Boy132\MinecraftModrinth\Filament\Server\Pages;

use Boy132\MinecraftModrinth\Enums\ModrinthProjectType;

class MinecraftModrinthModPage extends MinecraftModrinthProjectPage
{
    protected static ?ModrinthProjectType $modrinthProjectType = ModrinthProjectType::Mod;

    protected static ?string $slug = 'modrinth_mods';
}
