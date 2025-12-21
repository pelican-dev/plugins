# Minecraft Modrinth

Easily download and install Minecraft mods and plugins directly from Modrinth within the server panel.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `minecraft-modrinth` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |
| **Panels** | server |

## Features

- Browse and search Modrinth's extensive mod library
- Download mods and plugins directly to your server
- Filter by project type (mods, plugins, resource packs, etc.)
- Automatic version compatibility checking
- Seamless installation to the correct server directory

## Technical Details

### Enums

- `ModrinthProjectType` - Types of projects available on Modrinth

### Services

- `MinecraftModrinthService` - Handles API communication with Modrinth

### Facades

- `MinecraftModrinth` - Static access to the Modrinth service

### Filament Pages

| Panel | Page |
|-------|------|
| Server | `MinecraftModrinthProjectPage` - Browse and install mods |

### Configuration

```php
// config/minecraft-modrinth.php
// Contains Modrinth API configuration
```

### Plugin Structure

```
minecraft-modrinth/
├── plugin.json
├── config/
│   └── minecraft-modrinth.php
└── src/
    ├── MinecraftModrinthPlugin.php
    ├── Enums/
    │   └── ModrinthProjectType.php
    ├── Facades/
    │   └── MinecraftModrinth.php
    ├── Services/
    │   └── MinecraftModrinthService.php
    └── Filament/
        └── Server/
            └── Pages/
                └── MinecraftModrinthProjectPage.php
```

## Dependencies

None
