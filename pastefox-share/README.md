# PasteFox Share (by FlexKleks)

Share console logs via [pastefox.com](https://pastefox.com) with one click.

## Features

- One-click log sharing from server console
- Optional API key for extended features (without API key, pastes expire after 7 days)
- Configurable visibility (PUBLIC/PRIVATE - requires API key)
- Visual effects (Matrix, Confetti, Glitch, etc.)
- Theme selection (Light/Dark)
- Password protection support
- Custom domain support
- Fetches up to 5000 log lines

## Configuration

1. Go to **Admin → Plugins**
2. Find **PasteFox Share** and click the **Settings** (gear icon) button
3. Configure the following settings:

| Setting       | Description                                        |
|---------------|----------------------------------------------------|
| API Key       | Optional - Get from https://pastefox.com/dashboard |
| Visibility    | PUBLIC or PRIVATE (requires API key)               |
| Effect        | Visual effect for the paste                        |
| Theme         | Light or Dark theme                                |
| Password      | Optional password protection                       |
| Custom Domain | Use your own domain for paste URLs                 |

### Without API Key
- Pastes expire after 7 days
- Always public visibility

### With API Key
- No expiration limit
- Private pastes available
- Effects
- Password protection
- Custom domain support

## Custom Domains

Use your own domain (e.g., `logs.yourdomain.com`) for sharing pastes.

1. Add and verify your domain at [PasteFox Dashboard → Custom Domains](https://pastefox.com/dashboard/domains)
2. Verify & Activate the domain in the PasteFox dashboard
3. Select the domain in the plugin settings

The plugin automatically falls back to `pastefox.com` if the configured domain becomes unavailable or inactive.

## Usage

1. Open a server console
2. Click the **"Share Logs"** button in the header
3. Copy the generated link from the notification
