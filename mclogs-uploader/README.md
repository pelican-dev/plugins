# MCLogs Uploader

Upload server console logs to mclo.gs for easy sharing and troubleshooting.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `mclogs-uploader` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |
| **Panels** | server |

## Features

- One-click upload of console logs to mclo.gs
- Integrated action button in the server console
- Automatic URL generation for sharing logs
- Perfect for Minecraft server debugging and support

## Technical Details

### Filament Components

| Type | Component |
|------|-----------|
| Action | `UploadLogsAction` - Button component for uploading logs |

### Translations

- English (`lang/en/upload.php`)

### Plugin Structure

```
mclogs-uploader/
├── plugin.json
├── lang/
│   └── en/
│       └── upload.php
└── src/
    ├── MclogsUploaderPlugin.php
    ├── Providers/
    │   └── MclogsUploaderPluginProvider.php
    └── Filament/
        └── Components/
            └── Actions/
                └── UploadLogsAction.php
```

### Integration

The plugin adds an action button to the server console that:
1. Captures the current console output
2. Sends it to the mclo.gs API
3. Returns a shareable URL for the uploaded log

## Dependencies

None
