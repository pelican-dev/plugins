# MCLogs Uploader (by Boy132)

Upload server console logs to mclo.gs for easy sharing and troubleshooting.

## Features

- One-click upload of console logs to mclo.gs
- Integrated action button in the server console
- **Tag-based visibility**: Show upload button only for specific eggs
- Perfect for (but not limited to) Minecraft servers

## Configuration

### Tag-Based Filtering

The upload button can be configured to only appear for servers using eggs with the `mclogs-updater` tag.

#### How to enable:

1. Navigate to the Egg configuration in your Pelican Panel
2. Add the tag `mclogs-updater` to eggs that support log uploading
3. The upload button will automatically appear only on servers using tagged eggs

**Note:** If no eggs have the `mclogs-updater` tag, the button will be hidden for all servers. This ensures the plugin only appears where it's actually needed.

## Usage

1. Navigate to your server's console page
2. Click the "Upload logs" button (visible only if your egg has the `mclogs-updater` tag)
3. The current console logs will be uploaded to mclo.gs
4. You'll receive a shareable link in a notification

## Requirements

- Pelican Panel
- Server must be online to upload logs
- Egg must have the `mclogs-updater` tag (for tag-based filtering)

## Installation

Install via Pelican Panel's plugin system or manually place in the plugins directory.

## Support

For issues or feature requests, please visit the [GitHub repository](https://github.com/pelican-dev/plugins/tree/main/mclogs-uploader).