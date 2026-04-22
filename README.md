# Pelican Plugins

A curated list of plugins for the [Pelican Panel](https://pelican.dev).

## How to install plugins

[Download the repository archive](https://github.com/pelican-dev/plugins/archive/refs/heads/main.zip) and extract the folders of the plugins you want to install to your panels `plugins` folder (`/var/www/pelican/plugins` by default). Finally, open your panel and head to the plugins page and click on "Install".

> [!IMPORTANT]
> The plugin folder and the plugin id need to match!  
> This means the plugin folder needs to be named the same as in this repo! (e.g. `player-counter` and _not_ `playercounter` or `plugins-player-counter`)

### Additional setup for themes

For themes you have to install Node.js 22+ and enable Corepack (which ships with Node.js and provides Yarn). Example:

```bash
curl -sL https://deb.nodesource.com/setup_22.x | sudo -E bash - 
sudo apt install -y nodejs

sudo corepack enable
```

## Plugins

- [Announcements](/announcements) - Create panel wide announcements to inform your users
- [Billing](/billing) - Allows users to purchase servers via Stripe - **Proof of Concept - Do absolutely NOT use in production!**
- [Generic OIDC Providers](/generic-oidc-providers) - Create generic OIDC providers for authentication
- [Legal Pages](/legal-pages) - Adds legal pages (Imprint, Privacy Policy, ToS) to the panel
- [MCLogs Uploader](/mclogs-uploader) - Upload console logs to mclo.gs
- [Minecraft Modrinth](/minecraft-modrinth) - Download Minecraft mods & plugins from Modrinth
- [Player Counter](/player-counter) - Show connected players count for game servers
- [Register](/register) - Enable user self-registration on all panels
- [Robo Avatars](/robo-avatars) - Adds RoboHash as avatar provider
- [Rust uMod](/rust-umod) - Download Rust plugins from uMod
- [Snowflakes](/snowflakes) - Adds CSS snowflakes to all panels
- [Subdomains](/subdomains) - Create Cloudflare subdomains for servers
- [Tawk.to Widget](/tawkto-widget) - Adds a Tawk.to live chat widget
- [Theme Customizer](/theme-customizer) - Customize panel font and colors
- [Tickets](/tickets) - Simple ticket system for user support
- [User Creatable Servers](/user-creatable-servers) - Allow users to create their own servers

## Themes

- [Fluffy Theme](/fluffy-theme) - A super nice and super fluffy theme
- [Neobrutalism Theme](/neobrutalism-theme) - Transform your panel with thick borders, pronounced shadows, and geometric aesthetics inspired by the neobrutalism design movement
- [Nord Theme](/nord-theme) - A light and dark arctic Nord theme
- [Pterodactyl Theme](/pterodactyl-theme) - Pterodactyl like colors and font

## Language Packs

- [Pirate Language](/pirate-language) - Turns yer site's lingo into pirate talk, matey!
