# Pelican Plugins

A curated list of plugins for the [pelican panel](https://pelican.dev). Feel free to add plugins that you created via a pull request!

## How to install plugins

[Download the repository archive](https://github.com/pelican-dev/plugins/archive/refs/heads/main.zip) and extract the folders of the plugins you want to install to your panels `plugins` folder (`/var/www/pelican/plugins` by default). Finally, open your panel and head to the plugins page and click on "Install".

## Plugin list

### [Announcements](/announcements) <sup><sub>by Boy132</sub></sup>

Very simple announcements via AlertBanners.

> [!WARNING]
> Alert banners are currently bugged. See <https://github.com/pelican-dev/panel/issues/1492>

---

### [Billing](/billing) <sup><sub>by Boy132</sub></sup>

Allows admins to create products and users to buy them. Features automatic server creation and payment handling. Also adds a simple shop.

> [!CAUTION]
> This plugin is incomplete!

---

### [Fluffy Theme](/fluffy-theme) <sup><sub>by Boy132</sub></sup>

Simple "Theme" with silly colors and font.

---

### [Generic OIDC Providers](/generic-oidc-providers) <sup><sub>by Boy132</sub></sup>

Allows to create generic OIDC providers.

---

### [Legal Pages](/legal-pages) <sup><sub>by Boy132</sub></sup>

Adds some legal pages (like an Imprint) to the panel.

---

### [Mclogs Uploader](/mclogs-uploader) <sup><sub>by Boy132</sub></sup>

Adds a button to the console for uploading console logs to [mclo.gs](https://mclo.gs/).

---

### [Minecraft Modrinth](/minecraft-modrinth) <sup><sub>by Boy132</sub></sup>

Allows to easily download minecraft mods & plugins from modrinth.

Add `modrinth_mods` or `modrinth_plugins` to the features of your egg to enable the mod/ plugins page. Also make sure your egg has the `minecraft` tag and a tag for the mod loader, e.g. `paper` or `forge` or `fabric`.

---

### [Pirate Language](/pirate-language) <sup><sub>by Boy132</sub></sup>

Adds a new Pirate like language to the panel.

> [!WARNING]
> Only the server pages, the Admin Dashboard, Profile page & Health page are currently translated.

---

### [Player Counter](/player-counter) <sup><sub>by Boy132</sub></sup>

Adds a simple player counter to the console and a player list page. Uses a [GameQ fork](https://github.com/krymosoftware/gameq).

> [!IMPORTANT]
> You need to have the bz2 php extension and zip/ 7zip installed!

---

### [Pterodactyl Theme](/pterodactyl-theme) <sup><sub>by Boy132</sub></sup>

Simple "Theme" with Pterodactyl like colors and font.

---

### [Register](/register) <sup><sub>by Boy132</sub></sup>

Enables registration on all panels.

---

### [Robo Avatars](/robo-avatars) <sup><sub>by Boy132</sub></sup>

Adds [RoboHash](https://robohash.org/) as avatar provider.

---

### [Rust uMod](/rust-umod) <sup><sub>by Boy132</sub></sup>

Allows to easily download rust plugins from uMod.

---

### [Snowflakes](/snowflakes) <sup><sub>by Boy132</sub></sup>

Adds simple CSS snowflakes to all panels.

---

### [Subdomains](/subdomains) <sup><sub>by Boy132</sub></sup>

Allows users to create subdomains (on cloudflare) for their servers.

---

### [Tawk.to Widget](/tawkto-widget) <sup><sub>by Boy132</sub></sup>

Adds a Tawk.to widget to all panels.

---

### [Theme Customizer](/theme-customizer) <sup><sub>by Boy132</sub></sup>

Allows to customize the panel font and colors.

---

### [Tickets](/tickets) <sup><sub>by Boy132</sub></sup>

Allows users to create tickets (per server) and admins to answer them.

---

### [User Creatable Servers](/user-creatable-servers) <sup><sub>by Boy132</sub></sup>

Allows admins to assign resources to users. User can then create server themselves.
