<?php

namespace Ebnater\PocketIDProvider;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PocketIDProviderPlugin implements Plugin
{
    /**
     * Get the plugin's unique identifier.
     *
     * @return string The plugin identifier "pocketid-provider".
     */
    public function getId(): string
    {
        return 'pocketid-provider';
    }

    /**
     * Registers Filament resources for the given panel by discovering them under the plugin's Filament resources path.
     *
     * Discovers resources located in "src/Filament/<TitleCasedPanelId>/Resources" inside this plugin and maps them to the
     * PHP namespace "Ebnater\\PocketIDProvider\\Filament\\<TitleCasedPanelId>\\Resources".
     *
     * @param \Filament\Panel $panel The Filament panel whose resources should be discovered and registered.
     */
    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Ebnater\\PocketIDProvider\\Filament\\$id\\Resources");
    }

    /**
 * Execute boot-time initialization for the given Filament panel.
 *
 * @param Panel $panel The Filament panel instance the plugin is booting for.
 */
public function boot(Panel $panel): void {}
}