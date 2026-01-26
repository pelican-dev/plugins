# Player Counter (by Boy132)

Show the amount of connected players to game servers with real-time querying capabilities.

## Setup

**IMPORTANT**: You need to have the bz2 php extension and zip/7zip installed!

Make sure your server has an allocation with a public ip.

For Minecraft servers you need to set `enable-query` to true and the `query-port` to your server port! (in `server.properties`)  
Game query for FiveM/RedM is currently not available due to a [bug with GameQ](https://github.com/pelican-dev/plugins/issues/48).

## Features

- Real-time player count display for game servers
- Support for multiple game query protocols via [GameQ](https://github.com/krymosoftware/gameq)
- Link query protocols to specific eggs
- Dashboard widget showing connected players
- Dedicated players page for detailed information
- Configurable through the admin panel
- Advanced integration for Minecraft servers: Displays user helmet avatar and allows to manage whitelist & op list.
