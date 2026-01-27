# Player Counter (by Boy132)

Show the amount of connected players to game servers with real-time querying capabilities.

## Setup

Make sure your server has an allocation with a public ip. Alternatively, if you use local ips you can put the public ip in the allocation alias and enable "Use allocation alias?" in the plugin settings.

Minecraft servers will first try the query (which requires you to set `enable-query` to true and `query-port` to your server port in `server.properties`) and will fallback to ping. It is recommended to enable query.

## Features

- Real-time player count display for game servers
- Support for multiple game query protocols
- Link query protocols to specific eggs
- Dashboard widget showing connected players
- Dedicated players page for detailed information
- Configurable through the admin panel
- Advanced integration for Minecraft servers: Displays user helmet avatar and allows to manage whitelist & op list.
