# Minecraft Modrinth (by Boy132)

Easily download, update, and manage Minecraft mods and plugins directly from Modrinth within the server panel.

## Setup

Add `modrinth_mods` or `modrinth_plugins` to the _features_ of your egg to enable the mod/plugins page.
Also make sure your egg has the `minecraft` _tag_ and a _tag_ for the [mod loader](https://github.com/pelican-dev/plugins/blob/main/minecraft-modrinth/src/Enums/MinecraftLoader.php#L10-L16). (e.g. `paper` or `neoforge`)

## Features

- **Browse and Search**: Access Modrinth's extensive mod library with search and pagination
- **Smart Installation**: One-click install with automatic latest version selection
- **Status Tracking**: See which mods are installed directly in the Modrinth list
- **Update Detection**: Automatic detection of available updates with one-click upgrade
- **Easy Uninstall**: Remove mods/plugins with confirmation and automatic file cleanup
- **Metadata Management**: Tracks installed versions, filenames, and installation dates
- **Version Compatibility**: Automatic filtering by Minecraft version and mod loader
- **Seamless Installation**: Downloads to the correct server directory (mods/ or plugins/)
- **Multilingual**: Supports English and German translations

## How It Works

### Installing Mods/Plugins
1. Browse or search for mods in the Modrinth list
2. Click the **Install** button (download icon)
3. The latest compatible version is automatically downloaded and tracked

### Managing Installed Mods
- **Installed** (green check): Mod is installed and up-to-date
- **Update** (orange refresh): Newer version available - click to upgrade
- **Uninstall** (red trash): Remove mod from server

### Metadata Tracking
The plugin maintains a `.modrinth-metadata.json` file in your mods/plugins folder that tracks:
- Project ID and name
- Installed version ID and number
- Filename
- Installation date

This enables accurate update detection and prevents duplicate installations
