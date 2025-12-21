# User Creatable Servers

Allow users to create their own servers within defined resource limits set by administrators.

> [!IMPORTANT]
> Add `user_creatable_servers` as tag to the nodes that should be used for creating servers.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `user-creatable-servers` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Users can create servers without admin intervention
- Configurable resource limits per user (CPU, RAM, disk, etc.)
- Admin management of user resource allocations
- Resource usage overview widget for users
- Integration with existing server management

## Technical Details

### Database Migrations

| Migration | Description |
|-----------|-------------|
| `001_create_user_resource_limits_table.php` | Creates table for per-user resource limits |

### Models

- `UserResourceLimits` - Stores resource allocation limits for each user

### Policies

- `UserResourceLimitsPolicy` - Authorization rules for resource limit management

### Filament Resources

| Panel | Resource |
|-------|----------|
| Admin | `UserResourceLimitsResource` with `ManageUserResourceLimits` page |

### Filament Relation Managers

- `UserResourceLimitRelationManager` - Manage resource limits from user edit page

### Filament Widgets

| Panel | Widget |
|-------|--------|
| App | `UserResourceLimitsOverview` - Shows current resource usage vs. limits |

### Filament Pages

| Panel | Page |
|-------|------|
| Server | `ServerResourcePage` - Server resource management |

### Filament Actions

- `CreateServerAction` - Action component for creating new servers

### Configuration

```php
// config/user-creatable-servers.php
// Contains default limits and configuration options
```

### Environment Variables

| Variable | Description |
|----------|-------------|
| `UCS_CAN_USERS_UPDATE_SERVERS` | Allow users to modify their own servers |

### Translations

- English (`lang/en/strings.php`)

### Plugin Structure

```
user-creatable-servers/
├── plugin.json
├── config/
│   └── user-creatable-servers.php
├── database/
│   └── migrations/
│       └── 001_create_user_resource_limits_table.php
├── lang/
│   └── en/
│       └── strings.php
└── src/
    ├── UserCreatableServersPlugin.php
    ├── Models/
    │   └── UserResourceLimits.php
    ├── Policies/
    │   └── UserResourceLimitsPolicy.php
    ├── Providers/
    │   └── UserCreatableServersPluginProvider.php
    └── Filament/
        ├── Admin/
        │   └── Resources/
        │       ├── UserResourceLimits/
        │       └── Users/
        │           └── RelationManagers/
        ├── App/
        │   └── Widgets/
        │       └── UserResourceLimitsOverview.php
        ├── Server/
        │   └── Pages/
        │       └── ServerResourcePage.php
        └── Components/
            └── Actions/
                └── CreateServerAction.php
```

## Dependencies

None
