# Legal Pages

Adds essential legal pages (Imprint, Privacy Policy, Terms of Service) to the panel for compliance with legal requirements.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `legal-pages` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |
| **Panels** | admin, app |

## Features

- Pre-configured legal page types (Imprint, Privacy Policy, Terms of Service)
- Admin interface for editing legal page content
- Accessible pages in the user-facing app panel
- Markdown support for page content
- Customizable through the admin panel

## Technical Details

### Enums

- `LegalPageType` - Defines available legal page types

### Filament Pages

| Panel | Pages |
|-------|-------|
| Admin | `LegalPages` - Settings page for editing legal content |
| App | `Imprint`, `PrivacyPolicy`, `TermsOfService` - Public-facing pages |

### Views

- `base-page.blade.php` - Base template for rendering legal pages

### Translations

- English (`lang/en/strings.php`)

### Plugin Structure

```
legal-pages/
├── plugin.json
├── lang/
│   └── en/
├── resources/
│   └── views/
│       └── base-page.blade.php
└── src/
    ├── LegalPagesPlugin.php
    ├── Enums/
    │   └── LegalPageType.php
    ├── Providers/
    │   └── LegalPagesPluginProvider.php
    └── Filament/
        ├── Admin/
        │   └── Pages/
        │       └── LegalPages.php
        └── App/
            └── Pages/
                ├── BaseLegalPage.php
                ├── Imprint.php
                ├── PrivacyPolicy.php
                └── TermsOfService.php
```

## Dependencies

None
