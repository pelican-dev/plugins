# Fluffy Theme

A super nice and super fluffy theme that transforms the panel with playful colors and a fun font.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `fluffy-theme` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Theme |

## Features

- Custom "Finger Paint" font for a playful appearance
- Unique color palette with soft, fluffy tones
- Applies to all panels automatically

## Technical Details

### Color Palette

| Color | Value |
|-------|-------|
| Danger | Rose |
| Gray | Custom Fuchsia (desaturated) |
| Info | Violet |
| Primary | Indigo |
| Success | Teal |
| Warning | Pink |

### Plugin Structure

```
fluffy-theme/
├── plugin.json
└── src/
    └── FluffyThemePlugin.php
```

The plugin implements Filament's `Plugin` interface and customizes the panel appearance in the `register()` method by setting:
- Font family to "Finger Paint"
- Custom color scheme with soft, pastel-like tones

## Dependencies

None
