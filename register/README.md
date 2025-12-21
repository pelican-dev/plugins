# Register

A simple way for users to register themselves on the panel without administrator intervention.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `register` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Self-service user registration
- Integrated with Filament's authentication system
- Standard registration form with validation
- Seamless integration with existing login flow

## Technical Details

### Filament Pages

| Panel | Page |
|-------|------|
| Auth | `Register` - User registration form |

### How It Works

The plugin extends Filament's panel configuration to enable the registration functionality:

```php
$panel->registration(Register::class);
```

This adds a registration link to the login page and provides a complete registration form for new users.

### Plugin Structure

```
register/
├── plugin.json
└── src/
    ├── RegisterPlugin.php
    └── Filament/
        └── Pages/
            └── Auth/
                └── Register.php
```

## Dependencies

None
