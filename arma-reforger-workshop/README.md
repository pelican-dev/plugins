# Arma Reforger Workshop Plugin (by spolny)

A Pelican Panel plugin for managing Arma Reforger workshop mods directly from the server panel.

## Features

- **View Installed Mods** - See all mods currently configured in your server's `config.json`
- **Add Mods** - Add mods by entering their Workshop ID (GUID)
- **Remove Mods** - Remove mods from your server configuration
- **Browse Workshop** - Search and browse mods from the Bohemia Arma Reforger Workshop directly in the panel
- **One-Click Install** - Add mods to your server directly from the workshop browser
- **Mod Details** - View version, subscriber count, downloads, and ratings for each mod

## Requirements

Your server egg must have one of the following:
- Feature: `arma_reforger_workshop`
- Tag: `arma_reforger` or `arma-reforger`

## Configuration

The plugin automatically detects the server's config file location. By default, it looks for `config.json` in the server root.

You can customize the config path by adding a `CONFIG_FILE` server variable in your egg.

## How It Works

Arma Reforger servers use a `config.json` file where mods are listed in the `game.mods` array:

```json
{
  "game": {
    "mods": [
      {
        "modId": "5965550F24A0C152",
        "name": "Where Am I",
        "version": "1.2.0"
      }
    ]
  }
}
```

This plugin provides a user-friendly interface to manage this configuration without manually editing the JSON file.

## Workshop Integration

The plugin integrates with the [Bohemia Arma Reforger Workshop](https://reforger.armaplatform.com/workshop) to:

- Fetch mod details (name, version, subscribers, downloads, rating)
- Search for mods by name
- Browse popular mods
- Display mod thumbnails and descriptions

Mod data is cached to improve performance:
- Individual mod details: 6 hours
- Workshop search results: 15 minutes
