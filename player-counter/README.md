# Player Counter

Show the amount of connected players to game servers with real-time querying capabilities.

> [!IMPORTANT]
> You need to have the bz2 php extension and zip/7zip installed!

> [!IMPORTANT]
> For Minecraft servers you need to set `enable-query` to true and the `query-port` to your server port! (in `server.properties`)

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `player-counter` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |
| **Panels** | admin, server |

## Features

- Real-time player count display for game servers
- Support for multiple game query protocols via GameQ
- Link query protocols to specific eggs
- Dashboard widget showing connected players
- Dedicated players page for detailed information
- Configurable through the admin panel

## Dependencies

| Package | Version |
|---------|---------|
| `krymosoftware/gameq` | ^4.0 |
