# Player Counter (by Boy132 & Royal Multi Gamers)

Show the amount of connected players to game servers with real-time querying capabilities.

## Setup

**IMPORTANT**: You need to have the bz2 php extension and zip/7zip installed!

Make sure your server has an allocation with a public ip.

For Minecraft servers you need to set `enable-query` to true and the `query-port` to your server port! (in `server.properties`)  
Game query for FiveM/RedM is currently not available due to a [bug with GameQ](https://github.com/pelican-dev/plugins/issues/48).

### Post-Installation Steps

After installing or uninstalling this plugin, you **must** run the following commands:

```bash
php artisan optimize:clear
php artisan optimize
```

These commands clear and rebuild the application cache to ensure the plugin is properly loaded or removed.

## Features

- Real-time player count display for game servers
- Support for multiple game query protocols via [GameQ](https://github.com/krymosoftware/gameq)
- Link query protocols to specific eggs
- Dashboard widget showing connected players
- Dedicated players page for detailed information
- Configurable through the admin panel
- Advanced integration for Minecraft servers: Displays user helmet avatar and allows to manage whitelist & op list.

## Recent Modifications

### GameQ Integration & Game Query Management
- **Embedded GameQ Library**: Complete GameQ library integrated directly into the plugin for game server querying
- **Game Query Administration**: 
  - Create and manage game queries from the admin panel
  - Link specific query protocols to eggs
  - Configure query port offsets for different game types
  - Support for 100+ game protocols via GameQ
- **Automatic Egg Association**: 
  - Automatically detect and suggest appropriate query protocols based on egg tags and names (must be configured manually in the admin panel after installation, especially when adding new eggs)
  - Smart detection for Minecraft Java and Bedrock editions
  - Seamless integration with Pelican's egg system
- **Query Protocol Management**:
  - View and manage all configured game queries
  - Associate multiple eggs with the same query protocol
  - Configure custom port offsets per query type

### Minecraft Server Integration
- **Player Avatars**: Displays player helmet avatars using [Cravatar API](https://cravatar.eu) for visual identification
- **Whitelist Management**: 
  - Add or remove players from the server whitelist directly from the panel
  - Visual badge indicator for whitelisted players
  - Reads and modifies `whitelist.json` file
- **OP (Operator) Management**:
  - Grant or revoke operator permissions to players
  - Visual badge indicator for OP players
  - Reads and modifies `ops.json` file
- **Kick Player**: Ability to kick players from the server with a single click
- **Support for Both Editions**: Automatic detection and support for Minecraft Java Edition and Bedrock Edition

### Configuration Options
- **Use Alias Setting**: Option to use allocation alias instead of IP address for game queries
  - Configurable via admin panel settings
  - Environment variable: `PLAYER_COUNTER_USE_ALIAS`

### Players Page Enhancements
- **Search Functionality**: Search for specific players by name
- **Pagination**: Configurable pagination (30 or 60 players per page)
- **Grid Layout**: Responsive grid layout (1-3 columns depending on screen size)
- **Player Information Display**:
  - Player name with tooltip showing player ID (when available)
  - Connection time display for non-Minecraft servers
  - Status badges (Whitelisted, OP)
- **Quick Actions**: Context menu for each player with relevant actions (kick, whitelist, OP management)

### Server Status Integration
- **Offline Detection**: Displays appropriate message when server is offline
- **Real-time Updates**: Live player data with manual refresh capability
- **Empty State Handling**: Clear messaging when no players are online

### Technical Improvements
- **Automatic Egg Detection**: Smart detection of game type based on egg tags and names
- **Error Handling**: Comprehensive error handling with user-friendly notifications
- **File Repository Integration**: Direct integration with Pelican's daemon file repository for Minecraft file management
