# Theme Customizer

Allows administrators to customize the panel font and colors through an intuitive settings interface.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `theme-customizer` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Custom font support (upload TTF files)
- Customizable color palette
- Live preview of changes
- Persistent settings via environment variables
- Color options for: gray, primary, info, success, warning, danger

## Technical Details

### Configuration

```php
// config/theme-customizer.php
return [
    'font' => env('THEME_CUSTOMIZER_FONT'),
    'colors' => [
        'gray' => env('THEME_CUSTOMIZER_COLORS_GRAY'),
        'primary' => env('THEME_CUSTOMIZER_COLORS_PRIMARY'),
        'info' => env('THEME_CUSTOMIZER_COLORS_INFO'),
        'success' => env('THEME_CUSTOMIZER_COLORS_SUCCESS'),
        'warning' => env('THEME_CUSTOMIZER_COLORS_WARNING'),
        'danger' => env('THEME_CUSTOMIZER_COLORS_DANGER'),
    ],
];
```

### Environment Variables

| Variable | Description |
|----------|-------------|
| `THEME_CUSTOMIZER_FONT` | Name of the custom font file (without extension) |
| `THEME_CUSTOMIZER_COLORS_GRAY` | RGB value for gray color |
| `THEME_CUSTOMIZER_COLORS_PRIMARY` | RGB value for primary color |
| `THEME_CUSTOMIZER_COLORS_INFO` | RGB value for info color |
| `THEME_CUSTOMIZER_COLORS_SUCCESS` | RGB value for success color |
| `THEME_CUSTOMIZER_COLORS_WARNING` | RGB value for warning color |
| `THEME_CUSTOMIZER_COLORS_DANGER` | RGB value for danger color |

### Custom Fonts

To use a custom font:
1. Upload a TTF file to `storage/app/public/fonts/`
2. Select the font from the dropdown in plugin settings
3. The font will be applied to all panels

### Plugin Settings

The plugin implements `HasPluginSettings` interface with:
- Font selector with live preview
- Color pickers for each color category
- Badge previews showing selected colors

### Plugin Structure

```
theme-customizer/
├── plugin.json
├── config/
│   └── theme-customizer.php
└── src/
    └── ThemeCustomizerPlugin.php
```

## Dependencies

None
