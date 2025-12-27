# PasteFox Share

Share console logs via [pastefox.com](https://pastefox.com).

## Installation

1. Download and extract to `/var/www/pelican/plugins/pastefox-share`
2. Enable the plugin in Admin → Plugins
3. Add your PasteFox API key to `.env`:

```env
PASTEFOX_API_KEY=pk_your_api_key_here
PASTEFOX_VISIBILITY=PUBLIC
```

Get your API key from https://pastefox.com/dashboard

## Usage

1. Open a server console
2. Click the "Share Logs" button
3. Copy the generated link from the notification

## Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `PASTEFOX_API_KEY` | - | Your PasteFox API key (required) |
| `PASTEFOX_VISIBILITY` | `PUBLIC` | `PUBLIC` or `PRIVATE` |

## License

MIT
