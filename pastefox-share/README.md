# PasteFox Share

Share console logs via [pastefox.com](https://pastefox.com) with one click.

## Features

- One-click log sharing from server console
- Configurable visibility (PUBLIC/PRIVATE)
- Fetches up to 5000 log lines
- Admin settings page in sidebar

## Installation

1. Download and extract to `/var/www/pelican/plugins/pastefox-share`
2. Run `php artisan p:plugin:install`
3. Configure in Admin → Advanced → PasteFox

Get your API key from https://pastefox.com/dashboard

## Usage

1. Open a server console
2. Click the "Share Logs" button
3. Copy the generated link from the notification

## Coming Soon

- File sharing
- Custom domains
- Folders
- Syntax highlighting themes

## License

MIT
