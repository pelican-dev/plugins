# Rust uMod

Easily download and install Rust plugins directly from uMod (formerly Oxide) within the server panel.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `rust-umod` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |
| **Panels** | server |

## Features

- Browse and search uMod's Rust plugin library
- Download plugins directly to your Rust server
- One-click installation to the correct directory
- Search and filter plugins by name
- View plugin descriptions and documentation

## Technical Details

### Services

- `RustUModService` - Handles API communication with uMod

### Facades

- `RustUMod` - Static access to the uMod service

### Filament Pages

| Panel | Page |
|-------|------|
| Server | `RustUModPluginsPage` - Browse and install Rust plugins |

### Plugin Structure

```
rust-umod/
├── plugin.json
└── src/
    ├── RustUModPlugin.php
    ├── Facades/
    │   └── RustUMod.php
    ├── Services/
    │   └── RustUModService.php
    └── Filament/
        └── Server/
            └── Pages/
                └── RustUModPluginsPage.php
```

### Integration

The plugin connects to the uMod API to:
1. Fetch available Rust plugins
2. Display plugin information (name, description, author)
3. Download and install plugins to the server's plugin directory

## Dependencies

None
