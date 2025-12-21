# Pterodactyl Theme

A nostalgic theme that applies Pterodactyl-like colors and fonts to the panel for users familiar with the classic look.

## Metadata

| Property | Value |
|----------|-------|
| **ID** | `pterodactyl-theme` |
| **Author** | Boy132 |
| **Version** | 1.0.0 |
| **Category** | Theme |

## Features

- Pterodactyl-inspired color scheme
- IBM Plex Sans font for a clean, professional look
- Custom gray palette matching Pterodactyl's style
- Blue primary color theme
- Applies to all panels automatically

## Technical Details

### Fonts

| Font Type | Font Family |
|-----------|-------------|
| Primary | IBM Plex Sans |
| Mono | system-ui |
| Serif | sans-serif |

### Color Palette

The theme defines a custom gray palette using OKLCH color space for accurate Pterodactyl styling:

| Shade | Value |
|-------|-------|
| 50 | `oklch(0.975 0.0046 258.32)` |
| 100 | `oklch(0.9286 0.00618 254.9897)` |
| 200 | `oklch(0.8575 0.013 247.98)` |
| 300 | `oklch(0.718 0.0216 249.92)` |
| 400 | `oklch(0.6173 0.0232 249.98)` |
| 500 | `oklch(0.5297 0.0264 250.09)` |
| 600 | `oklch(0.4779 0.0267 246.6)` |
| 700 | `oklch(0.413 0.0288 246.77)` |
| 800 | `oklch(0.3656 0.027449 246.8348)` |
| 900 | `oklch(0.2753 0.0228 248.67)` |
| 950 | `oklch(0.2484 0.0128 248.51)` |

Primary color is set to Filament's Blue preset.

### Plugin Structure

```
pterodactyl-theme/
├── plugin.json
└── src/
    └── PterodactylThemePlugin.php
```

## Dependencies

None
