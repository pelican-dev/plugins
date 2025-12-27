# PasteFox Share

A plugin for [Pelican Panel](https://pelican.dev) to share console logs via [pastefox.com](https://pastefox.com) with one click.

## Features

- One-click log sharing from server console
- Optional API key for extended features (without API key, pastes expire after 7 days)
- Configurable visibility (PUBLIC/PRIVATE - requires API key)
- Visual effects (Matrix, Confetti, Glitch, etc.)
- Theme selection (Light/Dark)
- Password protection support
- Fetches up to 5000 log lines
- Admin settings page in sidebar

## Installation

1. Download the latest release ZIP file from the [Releases](https://github.com/FlexKleks/PelicanPlugins/releases) page
2. Go to your Pelican Panel admin area
3. Navigate to **Admin → Plugins**
4. Click **"Import file"**
5. Select the downloaded ZIP file
6. Click **"Import"**

### Clear Cache

```bash
php artisan optimize:clear
```

## Configuration

1. Go to **Admin → Advanced → PasteFox**
2. Configure the following settings:

| Setting | Description |
|---------|-------------|
| API Key | Optional - Get from https://pastefox.com/dashboard |
| Visibility | PUBLIC or PRIVATE (requires API key) |
| Effect | Visual effect for the paste |
| Theme | Light or Dark theme |
| Password | Optional password protection |

### Without API Key
- Pastes expire after 7 days
- Always public visibility
- Basic features only

### With API Key
- No expiration limit
- Private pastes available
- Password protection
- Paste linked to your account

## Usage

1. Open a server console
2. Click the **"Share Logs"** button in the header
3. Copy the generated link from the notification

## Coming Soon

- File sharing
- Custom domains
- Folders
- Syntax highlighting themes

## Author

Created by [FlexKleks](https://github.com/FlexKleks)

## Support

- [GitHub Issues](https://github.com/FlexKleks/PelicanPlugins/issues)
- [Pelican Discord](https://discord.gg/pelican-panel)

## License

MIT
