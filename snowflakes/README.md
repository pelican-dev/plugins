# Snowflakes

Let it snow, let it snow, let it snow! Adds a festive snowfall animation to the panel.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `snowflakes` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Plugin |

## Features

- Animated falling snowflakes across all panels
- Lightweight CSS-only animation (no JavaScript)
- Non-intrusive - doesn't affect panel functionality
- Perfect for holiday seasons or winter themes

## Technical Details

### How It Works

The plugin uses Filament's render hooks to inject snowflake elements and CSS animations into the page:

1. **PAGE_START hook** - Injects 12 snowflake div elements
2. **STYLES_BEFORE hook** - Injects the CSS animation styles

### Animation Details

- Uses CSS keyframe animations for smooth falling effect
- Each snowflake has a unique delay and position
- Snowflakes shake horizontally while falling
- Uses the `❅` character for snowflake appearance
- Positioned with `z-index: 9999` to appear above content
- Set to `pointer-events: none` to not interfere with interactions

### Plugin Structure

```
snowflakes/
├── plugin.json
└── src/
    ├── SnowflakesPlugin.php
    └── Providers/
        └── SnowflakesPluginProvider.php
```

### Credits

Animation source: [CSSnowflakes](https://pajasevi.github.io/CSSnowflakes/)

## Dependencies

None
