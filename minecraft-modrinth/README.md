# Minecraft Modrinth (by Boy132)

Easily download and install Minecraft mods and plugins directly from Modrinth within the server panel.

## Setup

Add `modrinth_mods` or `modrinth_plugins` to the _features_ of your egg to enable the mod/plugins page.  
Also make sure your egg has the `minecraft` _tag_ and a _tag_ for the [mod loader](https://github.com/pelican-dev/plugins/blob/main/minecraft-modrinth/src/Enums/MinecraftLoader.php#L10-L16). (e.g. `paper` or `neoforge`)

## Features

- Browse and search Modrinth's extensive mod library
- Download mods and plugins directly to your server
- Automatic version compatibility checking
- Seamless installation to the correct server directory
