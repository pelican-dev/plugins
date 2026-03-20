# Discord Webhooks (by notjami)

Send Discord webhook notifications for various server events in Pelican Panel.

## Features

- Discord webhook integration
- Server status notifications (online/offline)
- Multiple webhooks support
- Event-based triggers
- Automatic status checking via scheduled task

## Supported Events

- Server Started (online)
- Server Stopped (offline)
- Server Installing
- Server Installed

## Installation

1. Copy the `discord-webhooks` folder to your Pelican Panel plugins directory or import it with `https://github.com/jami100YT/pelican-plugins/archive/refs/tags/latest.zip`
2. Install the plugin

## Server Status Detection

Server start/stop detection works via a scheduled command that checks server status every minute.

**Manual check:**
```bash
php artisan webhooks:check-status
```

## Usage

1. Navigate to Admin Panel → Webhooks
2. Create a new webhook with your Discord webhook URL
3. Select which events should trigger the webhook
4. Save and test your webhook

## Configuration

Each webhook can be configured with:
- **Name**: A friendly name for the webhook
- **Webhook URL**: Your Discord webhook URL
- **Events**: Which events trigger this webhook
- **Enabled**: Toggle the webhook on/off
