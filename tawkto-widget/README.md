# Tawk.to Widget

Adds a Tawk.to live chat widget to all panels for real-time customer support.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `tawkto-widget` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Tawk.to live chat integration
- Appears on all panels (admin, app, server)
- Configurable through plugin settings
- Real-time customer support capability

## Technical Details

### Configuration

```php
// config/tawkto-widget.php
return [
    'provider_id' => env('TAWKTO_PROVIDER_ID'),
    'widget_id' => env('TAWKTO_WIDGET_ID'),
];
```

### Environment Variables

| Variable | Description |
|----------|-------------|
| `TAWKTO_PROVIDER_ID` | Your Tawk.to property ID |
| `TAWKTO_WIDGET_ID` | Your Tawk.to widget ID |

### Plugin Settings

The plugin implements `HasPluginSettings` interface, allowing configuration through the admin panel:

- **Provider ID** - Your Tawk.to property identifier
- **Widget ID** - The specific widget to embed

### Plugin Structure

```
tawkto-widget/
├── plugin.json
├── config/
│   └── tawkto-widget.php
└── src/
    ├── TawktoWidgetPlugin.php
    └── Providers/
        └── TawktoWidgetPluginProvider.php
```

### Getting Your Tawk.to IDs

1. Log in to your [Tawk.to Dashboard](https://dashboard.tawk.to/)
2. Go to Administration > Channels > Chat Widget
3. Find your Property ID and Widget ID in the embed code
4. Configure them in the plugin settings

## Dependencies

None
