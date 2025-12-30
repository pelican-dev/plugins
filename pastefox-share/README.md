# PasteFox Share (by FlexKleks)

Share console logs via [pastefox.com](https://pastefox.com) with one click.

## Features

- One-click log sharing from server console
- Optional API key for extended features (without API key, pastes expire after 7 days)
- Configurable visibility (PUBLIC/PRIVATE - requires API key)
- Visual effects (Matrix, Confetti, Glitch, etc.)
- Theme selection (Light/Dark)
- Password protection support
- Fetches up to 5000 log lines

## Configuration

1. Go to **Admin → Plugins**
2. Find **PasteFox Share** and click the **Settings** (gear icon) button
3. Configure the following settings:

| Setting    | Description                                        |
|------------|----------------------------------------------------|
| API Key    | Optional - Get from https://pastefox.com/dashboard |
| Visibility | PUBLIC or PRIVATE (requires API key)               |
| Effect     | Visual effect for the paste                        |
| Theme      | Light or Dark theme                                |
| Password   | Optional password protection                       |

### Without API Key
- Pastes expire after 7 days
- Always public visibility

### With API Key
- No expiration limit
- Private pastes available
- Password protection

## Usage

1. Open a server console
2. Click the **"Share Logs"** button in the header
3. Copy the generated link from the notification
