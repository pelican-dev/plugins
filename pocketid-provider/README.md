# Pocket ID Provider (by Ebnater)

This plugin allows you to use PocketID as an OAuth Provider for Pelican Panel.

## Features

- Register PocketID as OAuth Provider
- Integrates with the OAuth tab in Pelican Panel Settings
- Auto-create and auto-link users from PocketID

## Installation

### Standard Installation

1. Download the plugin and extract to your panel's `plugins` directory
2. Navigate to **Admin → Plugins** and click "Install" on PocketID Provider
3. Configure in **Admin → Settings → OAuth**

### Container/Kubernetes Installation

When running Pelican in a container (Docker, Kubernetes), you need to:

1. **Install the plugin** via the Admin UI or by mounting it to `/var/www/html/plugins/pocketid-provider`

2. **Install the composer dependency** - The plugin requires the `socialiteproviders/pocketid` package. For containerized deployments, you'll need to install this in an init container or custom image:
   ```bash
   composer require socialiteproviders/pocketid:^5.0 --ignore-platform-reqs
   ```

3. **Set environment variables** in your container configuration:

   | Variable | Required | Description |
   |----------|----------|-------------|
   | `OAUTH_POCKETID_CLIENT_ID` | Yes | OAuth Client ID from PocketID |
   | `OAUTH_POCKETID_CLIENT_SECRET` | Yes | OAuth Client Secret from PocketID |
   | `OAUTH_POCKETID_BASE_URL` | Yes | Your PocketID instance URL (e.g., `https://id.example.com`) |
   | `OAUTH_POCKETID_DISPLAY_NAME` | No | Button text (default: "Pocket ID") |
   | `OAUTH_POCKETID_DISPLAY_COLOR` | No | Button color hex code (default: "#000000") |
   | `OAUTH_POCKETID_SHOULD_CREATE_MISSING_USERS` | No | Auto-create users on first login (`true`/`false`) |
   | `OAUTH_POCKETID_SHOULD_LINK_MISSING_USERS` | No | Auto-link existing users by email (`true`/`false`) |

4. **Configure PocketID** - Create an OIDC client in your PocketID instance with:
   - **Callback URL**: `https://your-pelican-panel.com/auth/oauth/callback/pocketid`

### Example: Kubernetes Secret

```yaml
apiVersion: v1
kind: Secret
metadata:
  name: pelican-secret
stringData:
  OAUTH_POCKETID_CLIENT_ID: "your-client-id"
  OAUTH_POCKETID_CLIENT_SECRET: "your-client-secret"
  OAUTH_POCKETID_BASE_URL: "https://id.example.com"
  OAUTH_POCKETID_DISPLAY_NAME: "PocketID"
  OAUTH_POCKETID_SHOULD_CREATE_MISSING_USERS: "true"
  OAUTH_POCKETID_SHOULD_LINK_MISSING_USERS: "true"
```

### Example: Docker Compose

```yaml
services:
  pelican:
    image: ghcr.io/pelican-dev/panel:latest
    environment:
      OAUTH_POCKETID_CLIENT_ID: "your-client-id"
      OAUTH_POCKETID_CLIENT_SECRET: "your-client-secret"
      OAUTH_POCKETID_BASE_URL: "https://id.example.com"
      OAUTH_POCKETID_DISPLAY_NAME: "PocketID"
      OAUTH_POCKETID_SHOULD_CREATE_MISSING_USERS: "true"
      OAUTH_POCKETID_SHOULD_LINK_MISSING_USERS: "true"
```

## User Linking

- **Auto-link**: When `OAUTH_POCKETID_SHOULD_LINK_MISSING_USERS=true`, users logging in via PocketID will be automatically linked to existing Pelican accounts with matching email addresses.
- **Manual link**: Existing users can link their PocketID account from their profile settings under "Linked Accounts".

## Credits

- Plugin by [Ebnater](https://github.com/Ebnater)
- `PocketIDSchema.php` based on [devilr33f's pelican-pocketid repository](https://github.com/devilr33f/pelican-pocketid/blob/51292e9d52f31fd185fd1ba5a6d8fd34bf23a42c/patch/PocketIDSchema.php)
