# Pirate Language

Turns yer site's lingo into pirate talk, matey! A fun language pack that transforms the entire panel interface into pirate speak.

> [!WARNING]
> Only the server pages, the Admin Dashboard, Profile page & Health page are currently translated.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `pirate-language` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Language |

## Features

- Complete translation of panel interface to pirate speak
- Covers admin, server, and profile sections
- Fun and thematic for gaming communities
- Easy to enable through language settings

## Technical Details

### Translations

The plugin provides comprehensive translations in the `pirate` locale:

#### Admin Panel
- `admin/dashboard.php` - Dashboard strings
- `admin/health.php` - Health check strings
- `admin/plugin.php` - Plugin management strings

#### Server Panel
- `server/activity.php` - Activity log strings
- `server/backup.php` - Backup management strings
- `server/console.php` - Console strings
- `server/dashboard.php` - Server dashboard strings
- `server/database.php` - Database management strings
- `server/file.php` - File manager strings
- `server/network.php` - Network settings strings
- `server/schedule.php` - Schedule/task strings
- `server/setting.php` - Server settings strings
- `server/startup.php` - Startup configuration strings
- `server/user.php` - User management strings

#### General
- `profile.php` - User profile strings
- `exceptions.php` - Error message strings

### Plugin Structure

```
pirate-language/
├── plugin.json
├── lang/
│   └── pirate/
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── health.php
│       │   └── plugin.php
│       ├── server/
│       │   ├── activity.php
│       │   ├── backup.php
│       │   ├── console.php
│       │   ├── dashboard.php
│       │   ├── database.php
│       │   ├── file.php
│       │   ├── network.php
│       │   ├── schedule.php
│       │   ├── setting.php
│       │   ├── startup.php
│       │   └── user.php
│       ├── profile.php
│       └── exceptions.php
└── src/
    └── PirateLanguagePlugin.php
```

## Dependencies

None
