# Generic OIDC Providers

Allows administrators to create and configure generic OpenID Connect (OIDC) providers for authentication.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `generic-oidc-providers` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Create custom OIDC providers through the admin panel
- Configure client ID, client secret, and authorization endpoints
- Support for multiple OIDC providers simultaneously
- Seamless integration with Laravel Socialite

## Technical Details

### Database Migrations

| Migration | Description |
|-----------|-------------|
| `001_create_generic_oidc_providers_table.php` | Creates the `generic_oidc_providers` table |

### Models

- `GenericOIDCProvider` - Stores OIDC provider configurations

### Filament Resources

| Panel | Resource |
|-------|----------|
| Admin | `GenericOIDCProviderResource` with Create, Edit, and List pages |

### Extensions

- `GenericOIDCProviderSchema` - OAuth schema integration for the provider

### Translations

- English (`lang/en/strings.php`)

### Plugin Structure

```
generic-oidc-providers/
├── plugin.json
├── database/
│   └── migrations/
├── lang/
│   └── en/
└── src/
    ├── GenericOIDCProvidersPlugin.php
    ├── Models/
    ├── Providers/
    ├── Extensions/
    │   └── OAuth/
    │       └── Schemas/
    └── Filament/
        └── Admin/
            └── Resources/
```

## Dependencies

| Package | Version |
|---------|---------|
| `kovah/laravel-socialite-oidc` | ^0.5 |
