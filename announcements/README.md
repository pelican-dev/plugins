# Announcements

Create panel-wide announcements to inform your users about important updates, maintenance schedules, or any other information.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `announcements` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Create announcements with customizable titles and body text
- Support for different announcement types (info, warning, danger, success)
- Target specific panels (admin, app, server) or display on all panels
- Schedule announcements with valid from/to dates
- Announcements are displayed as alert banners across the panel

## Technical Details

### Database Migrations

| Migration | Description |
|-----------|-------------|
| `001_create_announcements_table.php` | Creates the `announcements` table |

### Database Schema

```
announcements
├── id (increments)
├── title (string)
├── body (string, nullable)
├── type (string, default: 'info')
├── panels (json, nullable)
├── valid_from (timestamp, nullable)
├── valid_to (timestamp, nullable)
└── timestamps
```

### Models

- `Announcement` - Eloquent model for managing announcements

### Policies

- `AnnouncementPolicy` - Authorization rules for announcement management

### Filament Resources

| Panel | Resource |
|-------|----------|
| Admin | `AnnouncementResource` with `ManageAnnouncements` page |

### Translations

- English (`lang/en/strings.php`)

## Dependencies

None
