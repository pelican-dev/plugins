# Player Counter

Show the amount of connected players to game servers with real-time querying capabilities.

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

## Technical Details

### Database Migrations

| Migration | Description |
|-----------|-------------|
| `001_create_game_queries_table.php` | Creates the `game_queries` table for query protocols |
| `002_create_egg_game_query_table.php` | Creates pivot table linking eggs to query protocols |

### Database Seeders

- `PlayerCounterSeeder` - Seeds default game query protocols

### Models

- `GameQuery` - Defines available game query protocols
- `EggGameQuery` - Pivot model linking eggs to query protocols

### Policies

- `GameQueryPolicy` - Authorization rules for game query management

### Filament Resources

| Panel | Resource |
|-------|----------|
| Admin | `GameQueryResource` with `ManageGameQueries` page |

### Filament Pages

| Panel | Page |
|-------|------|
| Server | `PlayersPage` - Displays connected players |

### Filament Widgets

| Panel | Widget |
|-------|--------|
| Server | `ServerPlayerWidget` - Dashboard widget for player count |

### Translations

- English (`lang/en/query.php`)

### Plugin Structure

```
player-counter/
├── plugin.json
├── database/
│   ├── Seeders/
│   │   └── PlayerCounterSeeder.php
│   └── migrations/
│       ├── 001_create_game_queries_table.php
│       └── 002_create_egg_game_query_table.php
├── lang/
│   └── en/
│       └── query.php
└── src/
    ├── PlayerCounterPlugin.php
    ├── Models/
    │   ├── GameQuery.php
    │   └── EggGameQuery.php
    ├── Policies/
    │   └── GameQueryPolicy.php
    ├── Providers/
    │   └── PlayerCounterPluginProvider.php
    └── Filament/
        ├── Admin/
        │   └── Resources/
        │       └── GameQueries/
        └── Server/
            ├── Pages/
            │   └── PlayersPage.php
            └── Widgets/
                └── ServerPlayerWidget.php
```

## Dependencies

| Package | Version |
|---------|---------|
| `krymosoftware/gameq` | ^4.0 |
