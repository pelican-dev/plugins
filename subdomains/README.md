# Subdomains

Allows users to create and manage custom subdomains for their game servers using Cloudflare DNS.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `subdomains` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |
| **Panels** | admin, server |

## Features

- Create custom subdomains for game servers
- Cloudflare DNS integration for automatic record management
- Admin management of Cloudflare domains
- Per-server subdomain limits
- User-friendly interface for subdomain management

## Technical Details

### Database Migrations

| Migration | Description |
|-----------|-------------|
| `001_create_cloudflare_domains_table.php` | Creates table for Cloudflare domain configurations |
| `002_create_subdomains_table.php` | Creates table for user-created subdomains |
| `003_add_subdomain_limit_to_servers.php` | Adds subdomain limit column to servers table |

### Models

- `CloudflareDomain` - Stores Cloudflare domain and API configurations
- `Subdomain` - User-created subdomain records

### Policies

- `CloudflareDomainPolicy` - Authorization for domain management

### Filament Resources

| Panel | Resource |
|-------|----------|
| Admin | `CloudflareDomainResource` with `ManageCloudflareDomains` page |
| Server | `SubdomainResource` with `ListSubdomains` page |

### Configuration

```php
// config/subdomains.php
// Contains Cloudflare API configuration
```

### Translations

- English (`lang/en/strings.php`)

### Plugin Structure

```
subdomains/
├── plugin.json
├── config/
│   └── subdomains.php
├── database/
│   └── migrations/
│       ├── 001_create_cloudflare_domains_table.php
│       ├── 002_create_subdomains_table.php
│       └── 003_add_subdomain_limit_to_servers.php
├── lang/
│   └── en/
│       └── strings.php
└── src/
    ├── SubdomainsPlugin.php
    ├── Models/
    │   ├── CloudflareDomain.php
    │   └── Subdomain.php
    ├── Policies/
    │   └── CloudflareDomainPolicy.php
    ├── Providers/
    │   └── SubdomainsPluginProvider.php
    └── Filament/
        ├── Admin/
        │   └── Resources/
        │       └── CloudflareDomains/
        └── Server/
            └── Resources/
                └── Subdomains/
```

## Dependencies

None
